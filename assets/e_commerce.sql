-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2024 at 10:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e_commerce`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteProductFromCart` (IN `p_userId` INT, IN `p_productId` INT)   BEGIN
    DECLARE cartId INT;

    -- Get the ID of the cart item to be deleted
    SELECT Id INTO cartId FROM cart WHERE UserId = p_userId AND cart.isShipped =0  LIMIT 1;

    -- Check if there are any remaining items associated with the same cart
    IF EXISTS (SELECT * FROM cartitems WHERE cartItemsId = cartId AND cartitems.productId = p_productId) THEN
        DELETE FROM cartitems WHERE cartItemsId = cartId AND cartitems.productId = p_productId;
    END IF;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertCheckout` (IN `p_userId` INT, IN `p_amount` DECIMAL(10,2))   BEGIN
	-- Declare a variable to hold the CartItemsId
    DECLARE cartItemsId INT;

    -- Get the CartItemsId associated with the provided user ID
    SELECT c.Id INTO cartItemsId
    FROM cart c
    JOIN cartitems ci ON ci.cartItemsId = c.Id
    WHERE c.UserId = p_userId AND c.isShipped =0 LIMIT 1;
    
    -- Insert a new record into the checkouts table
    INSERT INTO checkouts (UserId, CartItemsId, Amount)
    VALUES (p_userId, cartItemsId, p_amount);
    UPDATE cart set isShipped = true WHERE Id = cartItemsId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateCartAndCartItems` (IN `p_productId` INT, IN `p_items` INT, IN `p_userId` INT)   BEGIN
    DECLARE userExists INT;
    DECLARE productExists INT;
    DECLARE lastInsertedId INT;
    DECLARE cartId INT;

    -- Check if user exists in the Cart table
    SELECT COUNT(*) INTO userExists FROM Cart WHERE UserId= p_userId AND cart.isShipped =0;

    IF userExists > 0 THEN
        -- Check if the product exists in the CartItems table
        SELECT COUNT(*) INTO productExists FROM CartItems WHERE productId = p_productId;
        IF productExists > 0 THEN
            SELECT Id INTO cartId FROM Cart WHERE UserId = p_userId AND cart.isShipped =0;
            -- Product exists for the user, update quantity in CartItems
            UPDATE CartItems SET quantity = p_items WHERE productId = p_productId AND cartItemsId= cartId;
        ELSE
        	SELECT Id INTO cartId FROM Cart WHERE UserId = p_userId AND cart.isShipped =0;
            -- Product doesn't exist for the user, add new product in CartItems
            INSERT INTO CartItems (productId, quantity, cartItemsId) VALUES (p_productId, p_items, cartId);
        END IF;
        
    ELSE
        -- User doesn't exist, add new user and cart items
        INSERT INTO Cart (userId) VALUES (p_userId);
        -- Get the ID of the last inserted row in the Cart table
        SET lastInsertedId = LAST_INSERT_ID();
        -- Insert the last inserted ID into the CartItems table as cartItemsId
        INSERT INTO CartItems (productId, quantity, cartItemsId) VALUES (p_productId, p_items, lastInsertedId);
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `Id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `UserId` int(11) NOT NULL,
  `isShipped` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`Id`, `timestamp`, `UserId`, `isShipped`) VALUES
(24, '2024-02-22 07:28:39', 1234, 1),
(26, '2024-02-22 07:46:35', 1234, 1),
(27, '2024-02-22 07:48:18', 1234, 1),
(28, '2024-02-22 08:41:35', 1234, 1),
(29, '2024-02-22 09:06:03', 1234, 0);

-- --------------------------------------------------------

--
-- Table structure for table `cartitems`
--

CREATE TABLE `cartitems` (
  `Id` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cartItemsId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cartitems`
--

INSERT INTO `cartitems` (`Id`, `productId`, `quantity`, `cartItemsId`) VALUES
(18, 1, 4, 24),
(19, 2, 2, 24),
(21, 9, 4, 26),
(23, 11, 3, 26),
(25, 4, 6, 27),
(26, 1, 3, 28),
(27, 3, 2, 28),
(28, 3, 3, 29);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `parent_category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `parent_category_id`) VALUES
(1, 'Electronics', NULL),
(2, 'Clothing', NULL),
(3, 'Books', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `checkouts`
--

CREATE TABLE `checkouts` (
  `Id` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `CartItemsId` int(11) NOT NULL,
  `Amount` int(11) NOT NULL,
  `PaymentMethod` varchar(150) NOT NULL DEFAULT 'Cash on Delivery'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkouts`
--

INSERT INTO `checkouts` (`Id`, `UserId`, `CartItemsId`, `Amount`, `PaymentMethod`) VALUES
(4, 1234, 24, 5794, 'Cash on Delivery'),
(5, 1234, 26, 6236, 'Cash on Delivery'),
(6, 1234, 27, 6600, 'Cash on Delivery'),
(7, 1234, 28, 5397, 'Cash on Delivery');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `colors` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `subcategory_id`, `price`, `description`, `image`, `size`, `colors`) VALUES
(1, 'iPhone 13', 1, 999.00, 'Latest smartphone with advanced features.', 'https://img.freepik.com/free-vector/realistic-smartphone-device_52683-29765.jpg?size=626&ext=jpg&ga=GA1.1.1391092918.1704647654&semt=ais', 'Max', 'PINK'),
(2, 'Samsung Galaxy', 1, 899.00, 'High-performance Android smartphone.', 'https://img.freepik.com/free-photo/bubble-art-screen-smartphone-digital-device-experimental-art-with-design-space_53876-124749.jpg?size=626&ext=jpg&ga=GA1.1.1391092918.1704647654&semt=ais', 'Medium', 'WHITE'),
(3, 'HP Pavilion', 2, 1200.00, 'Powerful laptop for professional use.', 'https://img.freepik.com/free-vector/realistic-black-smartphone-front-back_52683-30239.jpg?size=626&ext=jpg&ga=GA1.1.1391092918.1704647654&semt=ais', 'Normal', 'BLACK'),
(4, 'Dell Inspiron', 2, 1100.00, 'Affordable laptop for everyday tasks.', 'https://img.freepik.com/free-photo/laptop-computer-night_1101-415.jpg?size=626&ext=jpg&ga=GA1.1.1391092918.1704647654&semt=ais', 'Medium', 'BLACK'),
(5, 'Polo T-shirt', 3, 25.00, 'Comfortable cotton t-shirt in various colors.', 'https://img.freepik.com/free-vector/collection-3d-realistic-white-black-polo-t-shirts-short-sleeves-fashion-design_1441-2395.jpg?size=626&ext=jpg&ga=GA1.1.1391092918.1704647654&semt=ais', 'X Large', 'WHITE, BLACK'),
(6, 'Levi\'s Jeans', 4, 50.00, 'Classic denim jeans for men and women.', 'https://img.freepik.com/free-photo/jeans_1203-8092.jpg?size=626&ext=jpg&ga=GA1.1.1391092918.1704647654&semt=ais', 'Medium, Small', 'BLUE'),
(8, 'Sapiens: A Brief History of Humankind', 6, 20.00, 'Bestselling book by Yuval Noah Harari.', 'https://img.freepik.com/free-psd/books-stacked-isolated-transparent-background_191095-17333.jpg?size=626&ext=jpg&ga=GA1.1.1391092918.1704647654&semt=sph', '---', '---'),
(9, 'MacBook Pro', 2, 1499.00, 'High-performance laptop by Apple for professionals.', 'https://img.freepik.com/free-photo/still-life-books-versus-technology_23-2150062920.jpg?size=626&ext=jpg&ga=GA1.1.1391092918.1704647654&semt=ais', 'Medium Display', 'BLACK & WHITE'),
(10, 'Google Pixel 6', 1, 799.00, 'Flagship Android smartphone with advanced camera features.', 'https://img.freepik.com/free-photo/white-cell-phone-box-background_58702-4717.jpg?size=626&ext=jpg&ga=GA1.1.1391092918.1704647654&semt=ais', 'Normal', 'STEEL BLUE'),
(11, 'Adidas Sneakers', 3, 80.00, 'Stylish and comfortable sneakers for everyday wear.', 'https://img.freepik.com/free-photo/fashion-shoes-sneakers_1203-7529.jpg?size=626&ext=jpg&ga=GA1.1.1391092918.1704647654&semt=ais', 'Large, Medium, Small', 'WHITE & GREY'),
(12, 'Nike Joggers', 4, 60.00, 'Athletic jogger pants for workouts and casual wear.', 'https://img.freepik.com/free-photo/pair-trainers_144627-3799.jpg?size=626&ext=jpg&ga=GA1.1.1391092918.1704647654&semt=sph', 'Medium, Small', 'WHITE & BLUE');

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `subcategory_id` int(11) NOT NULL,
  `subcategory_name` varchar(255) NOT NULL,
  `parent_category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`subcategory_id`, `subcategory_name`, `parent_category_id`) VALUES
(1, 'Smartphones', 1),
(2, 'Laptops', 1),
(3, 'T-shirts', 2),
(4, 'Jeans', 2),
(5, 'Fiction', 3),
(6, 'Non-fiction', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `cartitems`
--
ALTER TABLE `cartitems`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `productKeyVal` (`productId`),
  ADD KEY `cartItemsKey` (`cartItemsId`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `parent_category_id` (`parent_category_id`);

--
-- Indexes for table `checkouts`
--
ALTER TABLE `checkouts`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `CartItemsKeyVal` (`CartItemsId`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`subcategory_id`),
  ADD KEY `parent_category_id` (`parent_category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `cartitems`
--
ALTER TABLE `cartitems`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `checkouts`
--
ALTER TABLE `checkouts`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `subcategory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cartitems`
--
ALTER TABLE `cartitems`
  ADD CONSTRAINT `cartItemsKey` FOREIGN KEY (`cartItemsId`) REFERENCES `cart` (`Id`),
  ADD CONSTRAINT `productKeyVal` FOREIGN KEY (`productId`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `checkouts`
--
ALTER TABLE `checkouts`
  ADD CONSTRAINT `CartItemsKeyVal` FOREIGN KEY (`CartItemsId`) REFERENCES `cart` (`Id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`subcategory_id`);

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`parent_category_id`) REFERENCES `categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
