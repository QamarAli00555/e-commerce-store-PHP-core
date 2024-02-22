(function ($) {
  "use strict";

  // Get the input field
  const inputField = document.getElementById("filter-search");

  // Function to trigger the alert with the current text value
  function showAlert() {
    var searchText = inputField.value;
    var checkboxes = $(".filter-options input[type='checkbox']");

    checkboxes.each(function () {
      var label = $(this).parent();
      var labelText = label.text().toLowerCase();
      // Show/hide items based on the search text
      if (searchText === "" || labelText.includes(searchText.toLowerCase())) {
        label.show();
        if (label.is(":visible")) {
          label.next("br").show(); // Hide the <br> tag immediately after the visible label
        }
      } else {
        label.hide();
        if (!label.is(":visible")) {
          label.next("br").hide(); // Hide the <br> tag immediately after the visible label
        }
        //$("br").hide();
      }
    });
  }
  // Event listener to detect changes in the input field
  inputField.addEventListener("input", showAlert);

  $(document).on("click", ".sort-icon", function () {
    var $icon = $(this);
    var order = $icon.data("order");

    // Find the closest table to the clicked icon
    var $table = $icon.closest("table");

    var columnIndex = $icon.closest("th").index();
    var corder = order;

    // Toggle sorting order
    order = order === "asc" ? "desc" : "asc";
    $icon.data("order", order);

    // Toggle the sort icon based on the current sorting order
    if (order === "asc") {
      $icon.removeClass("fa-chevron-up").addClass("fa-chevron-down");
    } else {
      $icon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
    }

    // Sort the table rows
    var rows = $table.find("tbody tr").get();
    rows.sort(function (a, b) {
      var aValue = $(a)
        .find("td:eq(" + columnIndex + ")")
        .text();
      var bValue = $(b)
        .find("td:eq(" + columnIndex + ")")
        .text();

      // Check if both values are numeric
      var isNumeric =
        parseFloat(aValue) &&
        parseFloat(bValue) &&
        !isDate(aValue) &&
        !isDate(bValue);

      // Check if both values are date strings
      var isdt = isDate(aValue) || isDate(bValue);

      // Apply appropriate comparison logic based on data type
      if (isNumeric && !isdt) {
        console.log("Numeric");
        aValue = parseFloat(aValue);
        bValue = parseFloat(bValue);
      } else {
        aValue = aValue.toLowerCase();
        bValue = bValue.toLowerCase();
      }
      if (!isNumeric) {
        if (corder === "asc") {
          if (isdt) {
            var dateA = new Date(aValue.trim());
            var dateB = new Date(bValue.trim());
            if (isNaN(dateA.getTime())) {
              // aValue is not a valid date, so it comes before bValue
              return -1;
            } else if (isNaN(dateB.getTime())) {
              // bValue is not a valid date, so it comes before aValue
              return 1;
            } else {
              // Both values are valid dates; sort in descending order
              return dateA - dateB;
            }
            //          var aval = aValue.split(/[\n\s-]+/);
            //              aval=$.grep(aval, function (value) {
            //     return value !== "";
            // });
            //   console.log(aval);
            // var bval = bValue.split(/[\n\s-]+/);
            //              bval=$.grep(bval, function (value) {
            //     return value !== "";
            // });

            // datea
            // aval=10-18-2023  ,bval=09-12-2022
          } else {
            return aValue.toString().localeCompare(bValue.toString());
          }
        } else {
          if (isdt) {
            console.log(aValue);
            console.log(bValue);

            var dateAA = new Date(aValue.trim());
            var dateBB = new Date(bValue.trim());
            if (isNaN(dateAA.getTime())) {
              if (isNaN(dateBB.getTime())) {
                // Both values are invalid dates; keep them in the original order
                return 0;
              }
              // aValue is not a valid date, so it comes after bValue
              return 1;
            } else if (isNaN(dateBB.getTime())) {
              // bValue is not a valid date, so it comes before aValue
              return -1;
            } else {
              // Both values are valid dates; sort in descending order
              return dateBB - dateAA;
            }
            //          var av = aValue.split(/[\n\s-]+/);
            //              av=$.grep(av, function (value) {
            //     return value !== "";
            // });
            //   console.log(av);
            // var bv = bValue.split(/[\n\s-]+/);
            //              bv=$.grep(bv, function (value) {
            //     return value !== "";
            // });
            //         console.log(bv);
          } else {
            return bValue.toString().localeCompare(aValue.toString());
          }
        }
      } else {
        if (corder === "asc") {
          return aValue < bValue ? -1 : aValue > bValue ? 1 : 0;
        } else {
          return aValue > bValue ? -1 : aValue < bValue ? 1 : 0;
        }
      }
    });

    // Update the table with sorted rows
    $table.find("tbody").empty().append(rows);
    var currentUrl = window.location.href;

    var parts = currentUrl.split("/");

    // Get the last part of the URL
    var lastPart = parts.pop();
    if (
      lastPart == "PrimaryComponents.php" ||
      lastPart == "DoorComponents.php" ||
      lastPart == "ControlComponents.php" ||
      lastPart == "Accessories.php"
    ) {
      $("#myTable tbody tr").each(function () {
        // check if the first column of the current row contains a checkbox
        var checkbox = $(this).find('td:first-child input[type="checkbox"]');
        if (checkbox.length) {
          // get the value of the 9th column for the current row
          var column7Value = parseFloat(
            $(this).find("td:nth-child(10)").text()
          );

          if (column7Value >= 1) {
            // add buttons and value container after the checkbox in the first column of the current row
            //   var buttonContainer = '<div class="button-container">' +
            //                           '<span class="increment-button minus">-</span>' +
            //                           '<span class="increment-value">' + 0 + '</span>' +
            //                           '<span class="increment-button plus">+</span>' +
            //                         '</div>';
            //   checkbox.after(buttonContainer);

            // attach click event to the plus and minus buttons for the current row
            $(this)
              .find(".increment-button")
              .click(function () {
                var incrementValue = parseFloat(
                  $(this).parent().find(".increment-value").text()
                );
                if ($(this).hasClass("plus")) {
                  if (incrementValue < column7Value) {
                    var checkbox = $(this)
                      .closest("tr")
                      .find(".form-check .myCheckbox");
                    if (checkbox.prop("checked")) {
                    } else {
                      checkbox.prop("checked", true);
                    }

                    incrementValue++;
                  }
                  if (incrementValue == column7Value) {
                    $(this).hide();
                  }
                } else {
                  if (incrementValue >= 1) {
                    incrementValue--;
                    if ($(this).closest("tr").find(".plus").is(":hidden")) {
                      $(this).closest("tr").find(".plus").show();
                    }

                    if (incrementValue == 0) {
                      var checkbox = $(this)
                        .closest("tr")
                        .find(".form-check .myCheckbox");
                      if (checkbox.prop("checked")) {
                        checkbox.prop("checked", false);
                      }
                    }
                  }
                }
                $(this).parent().find(".increment-value").text(incrementValue);
              });
          }
        }
      });
    }
  });

  function isDate(dateString) {
    var regex =
      /^(0[0-9]|1[0-2]|[0-9])[-/](0[0-9]|[12][0-9]|3[01]|[0-9])[-/]\d{4}$/;

    return regex.test(dateString.trim());
  }

  //   $(".sort-icon").on("click", function () {
  //         var $icon = $(this);
  //         var order = $icon.data("order");
  //         var $table = $("#myTable");

  //       if ($("#myTable").length > 0) {
  //       $table = $("#myTable");
  //   console.log('table');
  //     } else  if($("#myTable1").length > 0) {
  //         // If "myTable1" doesn't exist, check for "myTable"
  //         $table = $("#myTable1");
  //       console.log('table 1');
  //     }else{
  //           $table = $("#myTable2");
  //           console.log('table 2');
  //     }
  //         var columnIndex = $icon.closest("th").index();
  // var corder=order;
  //         // Toggle sorting order
  //         order = order === "asc" ? "desc" : "asc";
  //         $icon.data("order", order);

  //                 // Toggle the sort icon based on the current sorting order
  //                 if (order === 'asc') {

  //                   $(this).removeClass('fa-chevron-up').addClass('fa-chevron-down');
  //                 } else {
  //                       $(this).removeClass('fa-chevron-down').addClass('fa-chevron-up');

  //                 }

  //         // Sort the table rows
  //         var rows = $table.find("tbody tr").get();
  //         rows.sort(function (a, b) {
  //             var aValue = $(a).find("td:eq(" + columnIndex + ")").text();
  //             var bValue = $(b).find("td:eq(" + columnIndex + ")").text();

  //             if (corder === "asc") {
  //                 return aValue.localeCompare(bValue);
  //             } else {
  //                 return bValue.localeCompare(aValue);
  //             }
  //         });

  //         // Update the table with sorted rows
  //         $table.find("tbody").empty().append(rows);

  //         //$table1
  //     });

  //  $('.sort-icon').on('click', function() {

  //                 var columnIndex = $(this).closest('th').index(); // Get the index of the parent header
  //                 var tbody = $('table tbody');
  //                 var rows = tbody.find('tr').toArray();
  //                 var currentOrder = $(this).data('order'); // Get the current sorting order

  //                 // Toggle the sorting order for the clicked column
  //                 if (currentOrder === 'asc') {
  //                     $(this).data('order', 'desc');
  //                 } else {
  //                     $(this).data('order', 'asc');
  //                 }

  //                 // Toggle the sort icon based on the current sorting order
  //                 if (currentOrder === 'asc') {
  //                      $(this).removeClass('fa-chevron-down').addClass('fa-chevron-up');

  //                 } else {
  //                   $(this).removeClass('fa-chevron-up').addClass('fa-chevron-down');
  //                 }

  //                 rows.sort(function(a, b) {
  //                     var aValue = $(a).find('td:eq(' + columnIndex + ')').text();
  //                     var bValue = $(b).find('td:eq(' + columnIndex + ')').text();

  //                     if (currentOrder === 'asc') {
  //                         return aValue.localeCompare(bValue);
  //                     } else {
  //                         return bValue.localeCompare(aValue);
  //                     }
  //                 });
  //                 tbody.empty();

  //                 $.each(rows, function(index, row) {
  //                     tbody.append(row);

  //                 });
  //             });

  ///FILTER

  var thindex = -1;
  var selectedFilters = {};

  $(".filter-icon").on("click", function () {
    $("#filter-search").val("");
    var anyUnchecked =
      $(".filter-options input[type='checkbox']:not(:checked)").length > 0;
    $("#select-all-checkbox").prop("checked", !anyUnchecked);

    //$("#select-all-checkbox").prop("checked", false);

    var $th = $(event.target).closest("th");
    thindex = $(event.target).closest("th").index();

    // Calculate the relative position of the dropdown

    var iconPosition = $(event.target).offset();
    var thPosition = $th.offset();
    var relativeTop =
      iconPosition.top - thPosition.top + $(event.target).height() + 5;
    var relativeLeft = iconPosition.left - thPosition.left;

    // Position the dropdown under the filter icon
    $(".filter-dropdown").css({
      top: iconPosition.top + $(".filter-icon").height() + 5,
      left: iconPosition.left,
    });

    var columnValues = [];
    $("tbody tr").each(function () {
      var cellValue = $(this).find("td").eq(thindex).text();

      if (columnValues.indexOf(cellValue) === -1 && cellValue != "") {
        columnValues.push(cellValue);
      }
    });

    var optionsHtml = "";

    var columnValues = columnValues.filter((value) => value.trim() !== "");
    for (var i = 0; i < columnValues.length; i++) {
      var isChecked = false;
      if (selectedFilters[thindex]) {
        for (let index = 0; index < selectedFilters[thindex].length; index++) {
          const element = selectedFilters[thindex][index];
          if (element.toLowerCase() === columnValues[i].toLowerCase()) {
            isChecked = true;
            break;
          }
        }
      }
      if (!selectedFilters[thindex]) {
        var selectAllValue = $("#select-all-checkbox").prop("checked");
        isChecked = selectAllValue;
      }
      optionsHtml +=
        '<label style="margin:auto"><input type="checkbox" ' +
        (isChecked ? "checked" : "") +
        ">" +
        columnValues[i] +
        "</label><br>";
    }

    // Add options to the filter dropdown and display it
    $(".filter-options").html(optionsHtml);
    $(".filter-dropdown").show();
  });

  // Assuming there's an event listener for checkbox changes within the dropdown
  $(".filter-options").on("change", "input[type='checkbox']", function () {
    // var anyUnchecked =
    //   $(".filter-options input[type='checkbox']:not(:checked)").length > 0;

    // if (anyUnchecked) {
    //   $("#select-all-checkbox").prop("checked", false);
    // }
    var allChecked =
      $(".filter-options input[type='checkbox']").length ===
      $(".filter-options input[type='checkbox']:checked").length;
    console.log($(".filter-options input[type='checkbox']").length);
    console.log($(".filter-options input[type='checkbox']:checked").length);
    $("#select-all-checkbox").prop("checked", allChecked);
  });

  $("#filter-icon-bom").on("click", function () {
    $("#select-all-checkbox").prop("checked", false);
    var $th = $(event.target).closest("th");
    thindex = $(event.target).closest("th").index();

    // Calculate the relative position of the dropdown
    var iconPosition = $(event.target).offset();
    var thPosition = $th.offset();
    var relativeTop =
      iconPosition.top - thPosition.top + $(event.target).height() + 5;
    var relativeLeft = iconPosition.left - thPosition.left;

    // Position the dropdown under the filter icon
    $(".filter-dropdown").css({
      top: iconPosition.top + $(".filter-icon").height() + 5,
      left: iconPosition.left,
    });

    var columnValues = [];
    $("tbody tr:visible").each(function () {
      var cellValue = $(this).find("td").eq(thindex).text();

      if (columnValues.indexOf(cellValue) === -1 && cellValue != "") {
        columnValues.push(cellValue);
      }
    });

    var optionsHtml = "";

    for (var i = 0; i < columnValues.length; i++) {
      var isChecked =
        selectedFilters[thindex] &&
        selectedFilters[thindex].includes(columnValues[i]);

      // Truncate the text to 10 characters
      var truncatedText = columnValues[i].substring(0, 15);

      optionsHtml +=
        '<label><input type="checkbox" ' +
        (isChecked ? "checked" : "") +
        "> " +
        truncatedText +
        "...</label><br>";
    }

    // Add options to the filter dropdown and display it
    $(".filter-options").html(optionsHtml);
    $(".filter-dropdown").show();
  });

  $(".ok-button").on("click", function () {
    var checkedValues = [];
    var selectAllCheckbox = document.getElementById("select-all-checkbox");
    var selectAllCheckboxValue = selectAllCheckbox.checked;

    // $(".filter-icon").eq(thindex).css("color", "red");
    $(".filter-icon").eq(thindex).css("color", "blue");
    // Iterate through the checkboxes in the filter dropdown
    $(".filter-options input[type='checkbox']").each(function () {
      if ($(this).is(":checked")) {
        checkedValues.push($(this).parent().text().trim().toLowerCase()); // Store checked values in lowercase
      } else {
        // If not checked, simply uncheck the checkbox without hiding the rows
        $(this).prop("checked", false);
      }
    });

    // Update the selected filters for the current column
    selectedFilters[thindex] = checkedValues;

    // Update the table rows based on selected filters
    if (checkedValues.length > 0) {
      $("tbody tr").each(function () {
        var shouldShow = false;
        var row = $(this);

        row.find("td").each(function (index) {
          if (index === thindex) {
            var cellValue = $(this).text().trim().toLowerCase(); // Get cell value in lowercase

            if (checkedValues.includes(cellValue)) {
              shouldShow = true;
              return false;
            }
          }
        });

        if (shouldShow) {
          row.show();
        } else {
          row.hide();
        }
      });
    } else {
      // If no filters are selected, show all rows
      $("tbody tr").show();
    }

    // Close the filter dropdown
    $(".filter-dropdown").hide();

    if (selectAllCheckboxValue) {
      $(".filter-icon").eq(thindex).css("color", "blue");
    } else {
      // $(".filter-icon").eq(thindex).css("color", "red");
      $(".filter-icon").eq(thindex).css("color", "blue");
    }
  });

  // Handle cancel button click
  $(".cancel-button").on("click", function () {
    $(".filter-options input[type='checkbox']").each(function () {
      $(this).show();
    });

    // Hide the filter dropdown
    $(".filter-dropdown").hide();
  });

  // SELECT ALL
  $("#select-all-checkbox").change(function () {
    var isChecked = $(this).is(":checked");

    //$(".filter-icon").eq(thindex).css("color", "blue");
    // Iterate through the checkboxes in the filter dropdown
    $(".filter-options input[type='checkbox']").each(function () {
      // Set the checked state of other checkboxes based on the "Select All" checkbox
      $(this).prop("checked", isChecked);
    });
    if (isChecked) {
      //selectedFilters = {}; // Clear the selected filters
      //$(".filter-icon").css("color", "blue");
    }
  });

  //Hiding ON Scroll

  $(".table-wrapper").on("scroll", function () {
    $(".filter-dropdown").hide();
  });

  //CLEARING FILTERS

  $("#clearfilters").click(function () {
    $("tbody tr").show(); // Show all rows in the tbody
    $(".filter-options input[type='checkbox']").prop("checked", false); // Uncheck all filter checkboxes
    selectedFilters = {}; // Clear the selected filters
    $(".filter-icon").css("color", "blue");
  });
})(jQuery);
