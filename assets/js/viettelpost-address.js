/**
 * Vietnam Address Fields Handler
 */
(function ($) {
  "use strict";

  $(document).ready(function () {
    // Only run on checkout page
    if (
      !$("body").hasClass("woocommerce-checkout") &&
      !$("body").hasClass("woocommerce-edit-address")
    ) {
      return;
    }

    // Flag to prevent infinite loops when restoring values
    var isRestoringValues = false;
    var lastRestoredValues = {
      billing: { province: null, district: null, ward: null },
      shipping: { province: null, district: null, ward: null },
    };

    // No shipping calculation flags here - that's handled by checkout.js

    // Force hide country fields completely
    function hideCountryFields() {
      $("#billing_country_field, #shipping_country_field").hide();
      $("#billing_country, #shipping_country").val("VN").hide();
      $(".country-field").hide();
      $('select[name="billing_country"], select[name="shipping_country"]')
        .val("VN")
        .hide();
    }

    // Hide immediately and repeatedly
    hideCountryFields();
    setInterval(hideCountryFields, 500);

    // Hide after checkout update
    $(document.body).on("updated_checkout", function () {
      // Prevent infinite loop
      if (isRestoringValues) {
        return;
      }

      hideCountryFields();
      $("#billing_country, #shipping_country").val("VN");

      // Store current address values to prevent them from being reset
      var billingProvince = $("#billing_state").val();
      var billingDistrict = $("#billing_city").val();
      var billingWard = $("#billing_address_2").val();
      var shippingProvince = $("#shipping_state").val();
      var shippingDistrict = $("#shipping_city").val();
      var shippingWard = $("#shipping_address_2").val();

      // Check if values actually changed - if not, skip restoration
      var billingChanged =
        billingProvince !== lastRestoredValues.billing.province ||
        billingDistrict !== lastRestoredValues.billing.district ||
        billingWard !== lastRestoredValues.billing.ward;

      var shippingChanged =
        shippingProvince !== lastRestoredValues.shipping.province ||
        shippingDistrict !== lastRestoredValues.shipping.district ||
        shippingWard !== lastRestoredValues.shipping.ward;

      // Only restore if values changed or fields are empty
      if (
        !billingChanged &&
        !shippingChanged &&
        billingProvince &&
        shippingProvince
      ) {
        return;
      }

      setTimeout(function () {
        convertFieldsToSelect();

        // Only load provinces if the select doesn't have options (only has placeholder)
        // This prevents resetting province when only district changes
        var billingStateHasOptions = $("#billing_state option").length > 1;
        var shippingStateHasOptions = $("#shipping_state option").length > 1;

        // Only reload provinces if they don't have options yet
        // If they already have options and a value is selected, preserve it
        if (!billingStateHasOptions) {
          loadProvinces();
        } else if (billingProvince) {
          var billingStateEl = document.getElementById("billing_state");
          if (billingStateEl && billingStateEl.value !== billingProvince) {
            // If options exist and province was selected, just restore it silently
            isRestoringValues = true;
            billingStateEl.value = billingProvince;
            isRestoringValues = false;
          }
        }

        if (!shippingStateHasOptions) {
          loadProvinces();
        } else if (shippingProvince) {
          var shippingStateEl = document.getElementById("shipping_state");
          if (shippingStateEl && shippingStateEl.value !== shippingProvince) {
            // If options exist and province was selected, just restore it silently
            isRestoringValues = true;
            shippingStateEl.value = shippingProvince;
            isRestoringValues = false;
          }
        }

        // Restore address values after loading provinces
        setTimeout(function () {
          isRestoringValues = true;

          // Use native JavaScript to set values without triggering jQuery events
          var billingStateEl = document.getElementById("billing_state");
          var billingCityEl = document.getElementById("billing_city");
          var billingWardEl = document.getElementById("billing_address_2");
          var shippingStateEl = document.getElementById("shipping_state");
          var shippingCityEl = document.getElementById("shipping_city");
          var shippingWardEl = document.getElementById("shipping_address_2");

          // Only restore if value is different
          if (
            billingProvince &&
            billingStateEl &&
            billingStateEl.value !== billingProvince
          ) {
            billingStateEl.value = billingProvince;
          }
          if (
            billingDistrict &&
            billingCityEl &&
            billingCityEl.value !== billingDistrict
          ) {
            billingCityEl.value = billingDistrict;
            // Reload wards if district is set
            if (billingDistrict && (!billingWardEl || !billingWardEl.value)) {
              loadWards(billingDistrict, "billing");
            }
          }
          if (
            billingWard &&
            billingWardEl &&
            billingWardEl.value !== billingWard
          ) {
            billingWardEl.value = billingWard;
          }

          if (
            shippingProvince &&
            shippingStateEl &&
            shippingStateEl.value !== shippingProvince
          ) {
            shippingStateEl.value = shippingProvince;
          }
          if (
            shippingDistrict &&
            shippingCityEl &&
            shippingCityEl.value !== shippingDistrict
          ) {
            shippingCityEl.value = shippingDistrict;
            // Reload wards if district is set
            if (
              shippingDistrict &&
              (!shippingWardEl || !shippingWardEl.value)
            ) {
              loadWards(shippingDistrict, "shipping");
            }
          }
          if (
            shippingWard &&
            shippingWardEl &&
            shippingWardEl.value !== shippingWard
          ) {
            shippingWardEl.value = shippingWard;
          }

          // Update last restored values
          lastRestoredValues.billing = {
            province: billingProvince,
            district: billingDistrict,
            ward: billingWard,
          };
          lastRestoredValues.shipping = {
            province: shippingProvince,
            district: shippingDistrict,
            ward: shippingWard,
          };

          isRestoringValues = false;
        }, 500);
      }, 300);
    });

    // Force set country to VN
    if (typeof wc_checkout_params !== "undefined") {
      wc_checkout_params.default_country = "VN";
    }

    // Convert text inputs to select if needed
    function convertFieldsToSelect() {
      // Convert state field to select if it's still a text input
      $("#billing_state, #shipping_state").each(function () {
        var $field = $(this);
        if ($field.is('input[type="text"]')) {
          var currentValue = $field.val();
          var $select = $("<select>", {
            id: $field.attr("id"),
            name: $field.attr("name"),
            class: "input-text viettelpost-province-select",
            "data-placeholder": "Chọn tỉnh/thành phố",
          });
          $select.append('<option value="">Chọn tỉnh/thành phố</option>');
          $field.replaceWith($select);
        }
      });

      // Convert city field to select if it's still a text input
      $("#billing_city, #shipping_city").each(function () {
        var $field = $(this);
        if ($field.is('input[type="text"]')) {
          var currentValue = $field.val();
          var $select = $("<select>", {
            id: $field.attr("id"),
            name: $field.attr("name"),
            class: "input-text viettelpost-district-select",
            "data-placeholder": "Chọn quận/huyện",
          });
          $select.append('<option value="">Chọn quận/huyện</option>');
          $field.replaceWith($select);
        }
      });

      // Convert address_2 field to select if it's still a text input
      $("#billing_address_2, #shipping_address_2").each(function () {
        var $field = $(this);
        if ($field.is('input[type="text"]')) {
          var currentValue = $field.val();
          var $select = $("<select>", {
            id: $field.attr("id"),
            name: $field.attr("name"),
            class: "input-text viettelpost-ward-select",
            "data-placeholder": "Chọn phường/xã",
          });
          $select.append('<option value="">Chọn phường/xã</option>');
          $field.replaceWith($select);
        }
      });
    }

    // Convert fields and load provinces on page load
    setTimeout(function () {
      convertFieldsToSelect();
      loadProvinces();
    }, 500);

    // Handle province change (Tỉnh/Thành phố) - First
    $(document.body).on(
      "change",
      "#billing_state, #shipping_state",
      function (e, silent) {
        // Skip if this is a silent restore
        if (silent === "silent" || isRestoringValues) {
          return;
        }

        var $field = $(this);
        var provinceId = $field.val();
        var fieldType =
          $field.attr("id").indexOf("billing") !== -1 ? "billing" : "shipping";

        // When province changes, clear and reload districts
        // Trigger event for checkout.js to handle

        if (provinceId) {
          // Clear district and ward first
          $("#" + fieldType + "_city")
            .html(
              '<option value="">' +
                wc_viettelpost_address.select_district +
                "</option>"
            )
            .val("");
          $("#" + fieldType + "_address_2")
            .html(
              '<option value="">' +
                wc_viettelpost_address.select_ward +
                "</option>"
            )
            .val("");

          // Then load districts for the selected province
          loadDistricts(provinceId, fieldType);
        } else {
          // If province is cleared, clear district and ward
          $("#" + fieldType + "_city")
            .html(
              '<option value="">' +
                wc_viettelpost_address.select_district +
                "</option>"
            )
            .val("");
          $("#" + fieldType + "_address_2")
            .html(
              '<option value="">' +
                wc_viettelpost_address.select_ward +
                "</option>"
            )
            .val("");
        }
        // Trigger event for checkout.js to handle shipping calculation
        $(document.body).trigger("viettelpost_address_changed", [fieldType]);
      }
    );

    // Handle district change (Quận/Huyện) - Second
    // IMPORTANT: Only load wards, NEVER modify province field
    $(document.body).on(
      "change",
      "#billing_city, #shipping_city",
      function (e, silent) {
        // Skip if this is a silent restore
        if (silent === "silent" || isRestoringValues) {
          return;
        }

        e.stopPropagation(); // Prevent any event bubbling
        e.stopImmediatePropagation(); // Prevent other handlers on same element

        var $field = $(this);
        var districtId = $field.val();
        var fieldType =
          $field.attr("id").indexOf("billing") !== -1 ? "billing" : "shipping";

        // Store current province value to prevent any changes
        var currentProvinceId = $("#" + fieldType + "_state").val();

        // Only handle ward loading, do NOT touch province field
        if (districtId) {
          // Load wards for the selected district
          loadWards(districtId, fieldType);
        } else {
          // Clear ward if district is cleared
          $("#" + fieldType + "_address_2")
            .html(
              '<option value="">' +
                wc_viettelpost_address.select_ward +
                "</option>"
            )
            .val("");
        }

        // Ensure province field is not changed (restore if somehow changed)
        // Use multiple timeouts to ensure it persists through checkout updates
        setTimeout(function () {
          var $provinceField = $("#" + fieldType + "_state");
          if (currentProvinceId && $provinceField.val() !== currentProvinceId) {
            $provinceField.val(currentProvinceId);
            console.log(
              "ViettelPost: Đã khôi phục tỉnh về giá trị ban đầu:",
              currentProvinceId
            );
          }
        }, 100);

        setTimeout(function () {
          var $provinceField = $("#" + fieldType + "_state");
          if (currentProvinceId && $provinceField.val() !== currentProvinceId) {
            $provinceField.val(currentProvinceId);
          }
        }, 600);

        setTimeout(function () {
          var $provinceField = $("#" + fieldType + "_state");
          if (currentProvinceId && $provinceField.val() !== currentProvinceId) {
            $provinceField.val(currentProvinceId);
          }
        }, 1200);

        // Trigger event for checkout.js to handle shipping calculation
        $(document.body).trigger("viettelpost_address_changed", [fieldType]);
      }
    );

    // Handle ward change (Phường/Xã) - Third - Calculate shipping when complete
    $(document.body).on(
      "change",
      "#billing_address_2, #shipping_address_2",
      function (e, silent) {
        // Skip if this is a silent restore
        if (silent === "silent" || isRestoringValues) {
          return;
        }

        var $field = $(this);
        var wardId = $field.val();
        var fieldType =
          $field.attr("id").indexOf("billing") !== -1 ? "billing" : "shipping";

        // When ward is selected, only calculate shipping if ALL fields are filled

        if (wardId) {
          var provinceId = $("#" + fieldType + "_state").val();
          var districtId = $("#" + fieldType + "_city").val();

          // Convert to integer if they are strings
          provinceId = provinceId ? parseInt(provinceId) : 0;
          districtId = districtId ? parseInt(districtId) : 0;
          wardId = wardId ? parseInt(wardId) : 0;

          // Trigger event for checkout.js to handle shipping calculation
          // Pass all address data
          $(document.body).trigger("viettelpost_address_complete", [
            {
              provinceId: provinceId,
              districtId: districtId,
              wardId: wardId,
              fieldType: fieldType,
            },
          ]);
        } else {
          // Clear shipping if any field is missing
          $(document.body).trigger("viettelpost_address_incomplete", [
            fieldType,
          ]);
        }
      }
    );

    // Also check on page load if all fields are already filled
    setTimeout(function () {
      checkAndTriggerShippingEvent();
    }, 1500);

    // Check after checkout update (with debounce)
    $(document.body).on("updated_checkout", function () {
      // Skip if we're restoring values to prevent loops
      if (isRestoringValues) {
        return;
      }

      // Debounce: only check after 1500ms of no updates
      setTimeout(function () {
        if (!isRestoringValues) {
          checkAndTriggerShippingEvent();
        }
      }, 1500);
    });

    /**
     * Check if all address fields are filled and trigger event for checkout.js
     * IMPORTANT: Only trigger when ALL three fields (province, district, ward) are filled
     */
    function checkAndTriggerShippingEvent() {
      // Check both billing and shipping
      ["billing", "shipping"].forEach(function (fieldType) {
        var provinceId = parseInt($("#" + fieldType + "_state").val()) || 0;
        var districtId = parseInt($("#" + fieldType + "_city").val()) || 0;
        var wardId = parseInt($("#" + fieldType + "_address_2").val()) || 0;

        // Only trigger if we have ALL three fields: province, district, and ward
        if (provinceId && districtId && wardId) {
          // Trigger event for checkout.js to handle shipping calculation
          $(document.body).trigger("viettelpost_address_complete", [
            {
              provinceId: provinceId,
              districtId: districtId,
              wardId: wardId,
              fieldType: fieldType,
            },
          ]);
        } else {
          // If any field is missing, trigger incomplete event
          $(document.body).trigger("viettelpost_address_incomplete", [
            fieldType,
          ]);
        }
      });
    }

    // Also handle country change to force VN
    $(document.body).on(
      "change",
      "#billing_country, #shipping_country",
      function () {
        $(this).val("VN");
        hideCountryFields();
      }
    );

    /**
     * Convert text input fields to select dropdowns
     */
    function convertFieldsToSelect() {
      // Convert state field to select if it's still a text input
      $("#billing_state, #shipping_state").each(function () {
        var $field = $(this);
        if ($field.is('input[type="text"]')) {
          var currentValue = $field.val();
          var $wrapper = $field.closest(".form-row");
          var $select = $("<select>", {
            id: $field.attr("id"),
            name: $field.attr("name"),
            class: "input-text viettelpost-province-select",
            "data-placeholder": "Chọn tỉnh/thành phố",
          });
          $select.append('<option value="">Chọn tỉnh/thành phố</option>');
          $field.replaceWith($select);
        }
      });

      // Convert city field to select if it's still a text input
      $("#billing_city, #shipping_city").each(function () {
        var $field = $(this);
        if ($field.is('input[type="text"]')) {
          var currentValue = $field.val();
          var $select = $("<select>", {
            id: $field.attr("id"),
            name: $field.attr("name"),
            class: "input-text viettelpost-district-select",
            "data-placeholder": "Chọn quận/huyện",
          });
          $select.append('<option value="">Chọn quận/huyện</option>');
          $field.replaceWith($select);
        }
      });

      // Convert address_2 field to select if it's still a text input
      $("#billing_address_2, #shipping_address_2").each(function () {
        var $field = $(this);
        if ($field.is('input[type="text"]')) {
          var currentValue = $field.val();
          var $select = $("<select>", {
            id: $field.attr("id"),
            name: $field.attr("name"),
            class: "input-text viettelpost-ward-select",
            "data-placeholder": "Chọn phường/xã",
          });
          $select.append('<option value="">Chọn phường/xã</option>');
          $field.replaceWith($select);
        }
      });
    }

    /**
     * Load provinces
     */
    function loadProvinces() {
      // Set country to VN first
      $("#billing_country, #shipping_country").val("VN");

      // Store current province values before loading
      var currentBillingProvince = $("#billing_state").val();
      var currentShippingProvince = $("#shipping_state").val();

      $.ajax({
        url: wc_viettelpost_address.ajax_url,
        type: "POST",
        data: {
          action: "viettelpost_get_provinces",
          nonce: wc_viettelpost_address.nonce,
        },
        success: function (response) {
          if (response.success && response.data) {
            // Update billing state
            var $billingState = $("#billing_state");
            if ($billingState.length) {
              var options =
                '<option value="">' +
                wc_viettelpost_address.select_province +
                "</option>";
              $.each(response.data, function (id, name) {
                options += '<option value="' + id + '">' + name + "</option>";
              });
              $billingState.html(options);

              // Restore previous value if it existed
              if (currentBillingProvince) {
                $billingState.val(currentBillingProvince);
              }
            }

            // Update shipping state
            var $shippingState = $("#shipping_state");
            if ($shippingState.length) {
              var options =
                '<option value="">' +
                wc_viettelpost_address.select_province +
                "</option>";
              $.each(response.data, function (id, name) {
                options += '<option value="' + id + '">' + name + "</option>";
              });
              $shippingState.html(options);

              // Restore previous value if it existed
              if (currentShippingProvince) {
                $shippingState.val(currentShippingProvince);
              }
            }
          }
        },
        error: function () {
          console.error("ViettelPost: Không thể load danh sách tỉnh/thành phố");
        },
      });
    }

    /**
     * Load districts
     */
    function loadDistricts(provinceId, fieldType) {
      var $districtField = $("#" + fieldType + "_city");
      $districtField
        .prop("disabled", true)
        .addClass("viettelpost-loading")
        .html('<option value="">Đang tải...</option>');

      $.ajax({
        url: wc_viettelpost_address.ajax_url,
        type: "POST",
        data: {
          action: "viettelpost_get_districts_frontend",
          province_id: provinceId,
          nonce: wc_viettelpost_address.nonce,
        },
        success: function (response) {
          $districtField
            .prop("disabled", false)
            .removeClass("viettelpost-loading");
          if (response.success && response.data) {
            var options =
              '<option value="">' +
              wc_viettelpost_address.select_district +
              "</option>";
            $.each(response.data, function (id, name) {
              options += '<option value="' + id + '">' + name + "</option>";
            });
            $districtField.html(options);
          } else {
            $districtField.html(
              '<option value="">' +
                wc_viettelpost_address.select_district +
                "</option>"
            );
            if (response.data && response.data.message) {
              console.error("ViettelPost:", response.data.message);
            }
          }
        },
        error: function () {
          $districtField
            .prop("disabled", false)
            .removeClass("viettelpost-loading")
            .html(
              '<option value="">' +
                wc_viettelpost_address.select_district +
                "</option>"
            );
          console.error("ViettelPost: Lỗi khi load quận/huyện");
        },
      });
    }

    /**
     * Load wards
     * IMPORTANT: This function should ONLY modify the ward field, never touch province or district
     */
    function loadWards(districtId, fieldType) {
      // Store current province and district values to prevent any changes
      var currentProvinceId = $("#" + fieldType + "_state").val();
      var currentDistrictId = $("#" + fieldType + "_city").val();

      var $wardField = $("#" + fieldType + "_address_2");
      $wardField
        .prop("disabled", true)
        .addClass("viettelpost-loading")
        .html('<option value="">Đang tải...</option>');

      $.ajax({
        url: wc_viettelpost_address.ajax_url,
        type: "POST",
        data: {
          action: "viettelpost_get_wards_frontend",
          district_id: districtId,
          nonce: wc_viettelpost_address.nonce,
        },
        success: function (response) {
          $wardField.prop("disabled", false).removeClass("viettelpost-loading");

          // Ensure province and district are not changed
          if (currentProvinceId) {
            $("#" + fieldType + "_state").val(currentProvinceId);
          }
          if (currentDistrictId) {
            $("#" + fieldType + "_city").val(currentDistrictId);
          }

          if (response.success && response.data) {
            var options =
              '<option value="">' +
              wc_viettelpost_address.select_ward +
              "</option>";
            $.each(response.data, function (id, name) {
              options += '<option value="' + id + '">' + name + "</option>";
            });
            $wardField.html(options);
          } else {
            $wardField.html(
              '<option value="">' +
                wc_viettelpost_address.select_ward +
                "</option>"
            );
            if (response.data && response.data.message) {
              console.error("ViettelPost:", response.data.message);
            }
          }
        },
        error: function () {
          $wardField
            .prop("disabled", false)
            .removeClass("viettelpost-loading")
            .html(
              '<option value="">' +
                wc_viettelpost_address.select_ward +
                "</option>"
            );

          // Ensure province and district are not changed even on error
          if (currentProvinceId) {
            $("#" + fieldType + "_state").val(currentProvinceId);
          }
          if (currentDistrictId) {
            $("#" + fieldType + "_city").val(currentDistrictId);
          }

          console.error("ViettelPost: Lỗi khi load phường/xã");
        },
      });
    }
  });
})(jQuery);
