angular
  .module("starter")
  .controller(
    "MigareferenceViewController",
    function (
      $scope,
      $stateParams,
      Migareference,
      Loader,
      Dialog,
      Customer,
      $rootScope,
      $translate,
      $state,
      SB,
      Modal
    ) {
      $scope.migareference = { page_title: "" };
      $scope.home_data = null;
      $scope.logs_array = {
        app_id: 0,
        user_id: 0,
        report_id: 0,
        log_type: "",
        log_detail: "",
      };
      $scope.is_logged_in = Customer.isLoggedIn();
      Loader.show();
      $scope.home_icos = null;
      $scope.user_id = 0;
      $scope.if_genrale_user = false;
      $scope.is_presettings = false;
      $scope.show_qlf_reserved_content = false;
      $scope.is_apikey_missing = false;
      $scope.showprizesmenu = false;
      $scope.isadmin = false;
      $scope.isagent = false;
      $scope.enable_report_referrer_behalf = false;
      $scope.presettings_warning = "";
      $scope.enrollShare = {
        message: "",
        url: "",
      };
      $scope.is_native_app = true;
      // GO to Home Page
      $scope.goToHomePage = function () {
        $state.go("home");
      };
      // calling login function
      $scope.login = function () {
        Customer.loginModal($scope);
      };
      // check if uaser is logged-in
      $scope.$on(SB.EVENTS.AUTH.loginSuccess, function () {
        $scope.is_logged_in = true;
      });
      // Loade Content
      ionic.Platform.isIOS();
      $scope.loadContent = function () {
        if (ionic.Platform.isIOS()) {
          $scope.platform = "IOS";
        } else if (ionic.Platform.isAndroid()) {
          $scope.platform = "Android";
        } else {
          $scope.platform = "Webview";
        }
        Migareference.load($stateParams.value_id, $scope.platform)
          .success(function (data) {
            $scope.migareference = data;
            if ($scope.is_logged_in) {
              $scope.user_id = Customer.customer.id;
              $scope.logs_array = {
                app_id: 0,
                user_id: $scope.user_id,
                report_id: 0,
                log_type: "Login",
                log_detail: "Login to App at",
              };
              $scope.saveLog($scope.logs_array);
            }
          })
          .error(function onError(data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
            $state.go("home");
          })
          .finally(function () {
            $scope.is_loading = false;
            Loader.hide();
          });
      };
      // Save LOGS
      $scope.saveLog = function (data) {
        Migareference.saveLog(data)
          .success(function (data) { })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.userType = function () {
        Migareference.userType(Customer.customer.id)
          .success(function (data) {
            Migareference.user_type = data;
          })
          .error(function () {
            $scope.is_loading = false;
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.userType();
      //Loade Home Contatn
      $scope.loadHomecontent = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadHomecontent()
          .success(function (data) {
            $scope.home_icos = Migareference.app_content = data.app_content;
          })
          .error(function () {
            $scope.is_loading = false;
            Loader.hide();
          })
          .finally(function () {
            $scope.is_loading = false;
            Loader.hide();
          });
      };
      // Get Notifications,Admin Type
      $scope.getNotifications = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getNotifications(Customer.customer.id)
          .success(function (data) {
            $scope.home_data = data;
            Migareference.pre_settings = data.pre_settings[0];
            if (data.is_presettings) {
              $scope.is_presettings = true;
              if (data.pre_settings[0].enable_report_referrer_behalf == 2) {
                $scope.enable_report_referrer_behalf = true;
              }
              if (data.pre_settings[0].reward_type == 2 && data.is_admin != 1) {
                $scope.showprizesmenu = true;
              }
            } else {
              $scope.presettings_warning = data.presettings_warning;
            }
            if (data.is_admin) {
              $scope.if_genrale_user = false;
              $scope.showprizesmenu = false;
              $scope.isadmin = true;
            } else if (data.is_agent) {
              $scope.if_genrale_user = true;
              $scope.isagent = true;
            } else {
              $scope.if_genrale_user = true;
            }
          })
          .error(function () {
            $scope.is_loading = false;
            Loader.hide();
          })
          .finally(function () {
            $scope.is_loading = false;
            Loader.hide();
          });
      };
      $scope.loadContent();
      $scope.getNotifications();
      $scope.loadHomecontent();
      $scope.goToHowitworks = function () {
        if ($scope.home_data.is_howto_data_missing == 1) {
          Dialog.alert(
            $translate.instant("Warning"),
            $scope.home_data.is_howto_data_missing_err,
            "OK"
          );
        } else if ($scope.home_data.is_apikey_missing == 1) {
          Dialog.alert(
            $translate.instant("Warning"),
            $scope.home_data.is_apikey_missing_err,
            "OK"
          );
        } else {
          $state.go("how-it-worksv3", {
            value_id: $stateParams.value_id,
          });
        }
      };
 

      $scope.goToQlfReservedContent = function () {
        $state.go("migareference-viewqlf", {
          value_id: $stateParams.value_id
        });      
      };


      $scope.gotoSettings = function () {
        if ($scope.is_presettings) {
          $state.go("property-settingsv2", {
            value_id: $stateParams.value_id,
          });
        } else {
          Dialog.alert(
            $translate.instant("Warning"),
            $scope.presettings_warning,
            "OK"
          );
        }
      };
      $scope.showreports = function () {
        $state.go("show-reportsv2", {
          value_id: $stateParams.value_id,
        });
      };
      $scope.showprizes = function () {
        $state.go("prize-shopv2", {
          value_id: $stateParams.value_id,
        });
      };
      $scope.showreminders = function () {
        $state.go("reminder-listv2", {
          value_id: $stateParams.value_id,
        });
      };
      $scope.showphonebooks = function () {
        $state.go("phonebook-listv2", {
          value_id: $stateParams.value_id,
        });
      };
      $scope.showstatistics = function () {
        // $state.go("general-stats", {
        //   value_id: $stateParams.value_id,
        // });
      };
      $scope.addReferrelreport = function () {
        if (!$scope.enable_report_referrer_behalf) {
          //it mean we have not shown the disabled baged as per pre_setting
          $state.go("referrer-report", {
            value_id: $stateParams.value_id,
            isagent: $scope.isagent,
            isadmin: $scope.isadmin,
          });
        }
      };
      $scope.getPropertysettings = function (report_type) {
        //report_type: 1 for Invite Propectus 2 for Instent save
        $scope.is_loading = true;
        Loader.show();
        Migareference.getPropertysettings(Customer.customer.id)
          .success(function (data) {
            //Set show_qlf_reserved_content
            $scope.show_qlf_reserved_content = data.is_qualified;
            $scope.qualified_badge_path = data.qualified.qlf_file;
            // ON App initiate apply sevral rules as per type of user
            if (data.read_only == 2) {
              // Rules of all users
              if (data.is_standard) {
                //No Status Settings exist
                Dialog.alert(data.error_text, data.is_standard_err, "OK");
              }
              //Admin Rules
              if (data.is_admin && report_type == 2) {
                //Admin user can not submit a report as a referrer
                Dialog.alert(data.error_text, data.is_admin_err, "OK");
              } else {
                //Referrer and Agents Rules
                var is_error = 0;
                if (!data.setup_settings && report_type == 2) {
                  //if dose not save profile settings
                  Dialog.alert(data.error_text, data.setup_settings_err, "OK");
                  setTimeout(function () {
                    $scope.gotoSettings();
                  }, 3000);
                }
                if (!data.is_blocked == 0) {
                  //Admin manually block this user
                  Dialog.alert(data.error_text, data.is_blocked_err, "OK");
                  is_error = 1;
                }
                if (data.is_need_vat_id) {
                  //User exceed limit
                  Dialog.alert(data.error_text, data.is_need_vat_id_err, "OK");
                  is_error = 1;
                }
                if (!is_error && report_type == 2) {
                  $state.go("submit-reportsv2", {
                    value_id: $stateParams.value_id,
                  });
                }
              }
            } else {
              Dialog.alert(data.error_text, data.read_only_err, "OK");
            }
          })
          .error(function () {
            $scope.is_loading = false;
            Loader.hide();
          })
          .finally(function () {
            $scope.is_loading = false;
            Loader.hide();
          });
      };
      $scope.shareEnrollUrl = function () {
        if (!$scope.home_data || !$scope.home_data.optin_settings) {
          Dialog.alert(
            $translate.instant("Warning"),
            $translate.instant(
              "Enroll URL settings are missing. Please complete the settings to share the message."
            ),
            "OK"
          );
          return;
        }
        var settings = $scope.home_data.optin_settings;
        var enrollUrl = settings.enrolling_page_url || "";
        var shareMessage = settings.enroll_sharing_message || "";
        if (!enrollUrl || !shareMessage) {
          var warningMessage =
            $scope.home_data.enroll_settings_warning ||
            $translate.instant(
              "Enroll URL settings are missing. Please complete the settings to share the message."
            );
          Dialog.alert($translate.instant("Warning"), warningMessage, "OK");
          return;
        }
        $scope.enrollShare = {
          message: shareMessage,
          url: enrollUrl,
        };
        $scope.is_native_app = !!$rootScope.isNativeApp;
        if ($scope.enrollModal) {
          $scope.enrollModal.show();
          return;
        }
        Modal.fromTemplateUrl("enrollshare.html", {
          scope: $scope,
        }).then(function (modal) {
          $scope.enrollModal = modal;
          $scope.enrollModal.show();
        });
      };

      $scope.shareEnrollNative = function () {
        if (!$rootScope.isNativeApp || !window.plugins || !window.plugins.socialsharing) {
          Dialog.alert(
            $translate.instant("Warning"),
            $translate.instant("This feature is disable on WebView!"),
            "OK"
          );
          return;
        }
        window.plugins.socialsharing.share($scope.enrollShare.message);
        $scope.closeEnrollModal();
      };

      $scope.shareEnrollSocial = function (share_action_type) {
        if (share_action_type === "cancel") {
          $scope.closeEnrollModal();
          return;
        }
        $scope.socialShare(share_action_type, $scope.enrollShare.message);
        if (share_action_type !== "copy") {
          $scope.closeEnrollModal();
        }
      };

      $scope.socialShare = function (share_action_type, share_message) {
        switch (share_action_type) {
          case "whatsapp":
            var encodedMessage = encodeURIComponent(share_message);
            var urltrigger = "https://api.whatsapp.com/send?text=" + encodedMessage;
            window.open(urltrigger, "_system");
            break;
          case "email":
            var subject = encodeURIComponent("");
            var emailBody = encodeURIComponent(share_message);
            var mailtoLink = "mailto:?subject=" + subject + "&body=" + emailBody;
            window.open(mailtoLink, "_system");
            break;
          case "sms":
            var smsMessage = encodeURIComponent(share_message);
            var smsLink = "sms:?body=" + smsMessage;
            window.open(smsLink, "_system");
            break;
          case "copy":
            var tempInput = document.createElement("input");
            tempInput.style.position = "absolute";
            tempInput.style.left = "-1000px";
            tempInput.value = share_message;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            break;
        }
      };

      $scope.closeEnrollModal = function () {
        if ($scope.enrollModal) {
          $scope.enrollModal.hide();
        }
      };

      $scope.$on("$destroy", function () {
        if ($scope.enrollModal) {
          $scope.enrollModal.remove();
        }
      });

      if ($scope.is_logged_in) {
        $scope.getPropertysettings(1);
      }
    }
  );
