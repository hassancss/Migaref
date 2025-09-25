angular
  .module("starter")
  .controller(
    "MigareferencereferrerreportController",
    function (
      Loader,
      $scope,
      $rootScope,
      $state,
      $stateParams,
      Customer,
      $translate,
      Migareference,
      Dialog,
      Modal,
      $sce
    ) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
        getPropertysettings: null,
        owner_warrning: 0,
        disablereportSubmit: false,
        home_icos: null,
        is_visible_invite_prospectus: false,
        is_visible_submit_report: false,
        property_detail_header: false,
        is_internal_report_alert: false,
        is_external_report_alert: false,
        form_builder: "",
        pre_settings: null,
        internal_report_note: "",
        external_report_note: "",
        tem_text: "",
        invite_message: "",
        jobform: { job_title: "" },
        origin: {
          address: null,
          latitude: null,
          longitude: null,
        },
      });

      $scope.countryitems = [];
      $scope.proviceitems = [];
      $scope.daysdateday = [
        {
          id: "",
          name: $translate.instant("Day"),
        },
        {
          id: "00",
          name: $translate.instant("N/A"),
        },
      ];
      for (d = 1; d <= 31; d++) {
        c = d;
        if (d < 10) {
          c = "0" + d;
        }
        $scope.daysdateday.push({ id: c, name: d });
      }
      $scope.months = [
        {
          id: "",
          name: $translate.instant("Month"),
        },
        {
          id: "00",
          name: $translate.instant("N/A"),
        },
        {
          id: "01",
          name: $translate.instant("January"),
        },
        {
          id: "02",
          name: $translate.instant("February"),
        },
        {
          id: "03",
          name: $translate.instant("March"),
        },
        {
          id: "04",
          name: $translate.instant("April"),
        },
        {
          id: "05",
          name: $translate.instant("May"),
        },
        {
          id: "06",
          name: $translate.instant("June"),
        },
        {
          id: "07",
          name: $translate.instant("July"),
        },
        {
          id: "08",
          name: $translate.instant("August"),
        },
        {
          id: "09",
          name: $translate.instant("September"),
        },
        {
          id: "10",
          name: $translate.instant("October"),
        },
        {
          id: "11",
          name: $translate.instant("November"),
        },
        {
          id: "12",
          name: $translate.instant("December"),
        },
      ];
      $scope.currunt_year = new Date().getFullYear();
      $scope.base_year = $scope.currunt_year - 80;
      $scope.years = [
        {
          id: "",
          name: $translate.instant("Year"),
        },
        {
          id: "0000",
          name: $translate.instant("N/A"),
        },
      ];
      for (y = $scope.base_year; y <= $scope.currunt_year; y++) {
        $scope.years.push({ id: y, name: y });
      }
      // Dynamics
      $scope.active_class_1 = "inactive_pile";
      $scope.active_class_2 = "inactive_pile";
      $scope.active_class_3 = "active_pile";
      $scope.active_class_4 = "inactive_pile";
      $scope.active_class_5 = "inactive_pile";
      $scope.page_title = "Add New Report";
      $scope.migareferencenewuser = {
        customer_id: Customer.customer.id,
        birth_day: "",
        birth_month: "",
        birth_year: "",
        mobile: "+39",
      };
      $scope.migareferesocialshare = {};
      $scope.migareferenceformchange = {
        user_id: Customer.customer.id,
        address: "",
        longitude: "",
        latitude: "",
        owner_hot: 3,
        owner_mobile: "+39",
        report_type: 2,
        report_custom_type: 1,
      };
      $scope.shareSocial = function () {
        if ($scope.migareferenceformchange.refreral_user_id) {
          var referrer =
            $scope.migareferenceformchange.refreral_user_id.split("@");
        } else {
          Dialog.alert(
            $translate.instant("Error"),
            $translate.instant("Please select choose a Referrer user"),
            "OK"
          );
          return true;
        }
        if (
          $stateParams.isadmin &&
          !$scope.migareferenceformchange.agent_user_id
        ) {
          var agent_id = 0;
        } else {
          var agent = $scope.migareferenceformchange.agent_user_id.split("@");
          var agent_id = agent[0];
        }
        if ($stateParams.isagent == true) {
          type = 3;
          agent_id = Customer.customer.id;
          referrer_id = referrer[0];
        } else {
          var type = 2;
          var referrer_id = referrer[0];
        }
        $scope.buildmessage(referrer_id, agent_id, type);
        if (!$rootScope.isNativeApp) {
          Dialog.alert(
            $translate.instant("Error"),
            $translate.instant("This feature is disable on WebView!"),
            "OK"
          );
          return true;
        }
        if ($scope.sharing_data == "") {
          Dialog.alert(
            $translate.instant("Error"),
            $translate.instant("Something went wrong, Please try again later!"),
            "OK"
          );
          return true;
        }
      };
      // Referrer id Agent id created by type
      $scope.buildmessage = function (referrer, agent_id, type) {
        Migareference.buildmessage(
          referrer,
          agent_id,
          Customer.customer.id,
          type
        )
          .success(function (data_build) {
            var raw_message = $scope.pre_settings.invite_message + " ";
            if (data_build.data.is_allow_socialshare == 2) {
              $scope.is_visible_invite_prospectus = false;
            }
            raw_message = raw_message.replace(
              "@@landing_link@@",
              data_build.data.app_url
            );
            raw_message = raw_message.replace(
              "@@agent_name@@",
              data_build.data.agent_data.firstname +
                " " +
                data_build.data.agent_data.lastname
            );
            $scope.invite_message = raw_message;
            window.plugins.socialsharing.share($scope.invite_message);
          })
          .error(function (data_build) {
            $scope.disablereportSubmit = false;
            Dialog.alert(
              $translate.instant("Warning"),
              data_build.message,
              "OK"
            );
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.disableTap = function (input_id) {
        var container = angular.element(
          document.getElementsByClassName("pac-container")
        );
        // disable ionic data tab
        container.attr("data-tap-disabled", "true");
        // leave input field if google-address-entry is selected
        container.on("click", function () {
          $log.debug(input_id);
          document.getElementById(input_id).blur();
        });
      };
      $scope.socialShare = function (share_action_type,share_message) {
        switch (share_action_type) {
          case 'whatsapp':
            //share on whatsapp
            var encodedMessage = encodeURIComponent(share_message); // Encode the message              
            urltrigger = "https://api.whatsapp.com/send?text=" + encodedMessage;
            window.open(urltrigger, '_system'); // Opens in a new tab or window              
            break;      
          case 'email':
            //share on Email
            var subject = encodeURIComponent(""); // Your email subject
            var emailBody = encodeURIComponent(share_message);
            var mailtoLink = `mailto:?subject=${subject}&body=${emailBody}`;              
            window.open(mailtoLink, '_system'); // Opens in the same window, you can use '_blank' to open in a new tab if preferred
            break;
          case 'sms':
            //share on SMS
            var encodedMessage = encodeURIComponent(share_message);
            var smsLink = `sms:?body=${encodedMessage}`;
            window.open(smsLink, '_system');
            break;
          case 'copy':
              // Create a hidden input element
              var tempInput = document.createElement('input');
              tempInput.style.position = 'absolute';
              tempInput.style.left = '-1000px';
              tempInput.value = share_message;
              document.body.appendChild(tempInput);
              // Select and copy the text inside the input element
              tempInput.select();
              document.execCommand('copy');
              // Remove the temporary input element
              document.body.removeChild(tempInput);
            break;
          case 'cancel':
            //cancel
            break;
        }
      };
      $scope.shareConsentSocial = function (share_action_type) {
        $scope.socialShare(share_action_type,$scope.consent_invit_msg_body);
        $scope.modal.hide();
      };
      $scope.shareConsent = function () {
        if (!$rootScope.isNativeApp) {
          // Open share dialog
          $scope.modal.hide();
          Modal.fromTemplateUrl("consentsocialshare.html", {
            scope: $scope,
          }).then(function (modal) {
            $scope.modal = modal;
            $scope.modal.show();           
          });  
          return true;
        }
        if ($scope.sharing_data == "") {
          Dialog.alert(
            $translate.instant("Error"),
            $translate.instant("Something went wrong, Please try again later!"),
            "OK"
          );
          $scope.modal.hide();
          return true;
        }
        $scope.modal.hide();
        return window.plugins.socialsharing.share(
          $scope.consent_invit_msg_body
        );
      };
      $scope.delayConsent = function () {
        $scope.modal.hide();
        $scope.goToMainMenu();
      };
      $scope.submitReport = function () {
        $scope.disablereportSubmit = true;
        $scope.is_loading = true;
        Migareference.savePropertyreport($scope.migareferenceformchange)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.consent_invit_msg_body = data.consent_invit_msg_body;
            $scope.migareferenceformchange = {
              user_id: Customer.customer.id,
              address: "",
              longitude: "",
              latitude: "",
              owner_hot: 3,
            };
            //Proceed consent Collectionz
            if ($scope.pre_settings.consent_collection == 1) {
              setTimeout(function () {
                $scope.openModel("consentcollection.html");
              }, 3000);
            } else {
              setTimeout(function () {
                $scope.goToMainMenu();
              }, 3000);
              setTimeout(function () {
                $scope.disablereportSubmit = false;
              }, 9000);
            }
          })
          .error(function (data) {
            $scope.disablereportSubmit = false;
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
       // This funstion is used for the pre validation of report data 
       $scope.prevalidatesubmitreport=function () {
        Migareference.prevalidatesubmitreport($scope.migareferenceformchange)
          .success(function (data) {
            Modal.fromTemplateUrl("reporttype.html", {
              scope: $scope,
            }).then(function (modal) {
              $scope.modal = modal;
              $scope.modal.show();
            });
          })
          .error(function (data) {
            $scope.disablereportSubmit = false;
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            Loader.hide();
            $scope.is_loading = false;
          });
      }
      $scope.savePropertyreport = function () {
        $scope.disablereportSubmit = true;                
          $scope.submitReport();        
      };
      $scope.setReportType = function (report_type) {
          $scope.migareferenceformchange.report_custom_type=report_type;
          $scope.modal.hide();          
      };
      $scope.disableTap = function (input_id) {
        var container = angular.element(
          document.getElementsByClassName("pac-container")
        );
        // disable ionic data tab
        container.attr("data-tap-disabled", "true");
        // leave input field if google-address-entry is selected
        container.on("click", function () {
          $log.debug(input_id);
          document.getElementById(input_id).blur();
        });
      };
      $scope.changeItinerary = function (id) {
        switch (id) {
          case 0:
            $scope.migareferenceformchange.latitude = $scope.origin.latitude;
            $scope.migareferenceformchange.longitude = $scope.origin.longitude;
            break;
          case 1:
            $scope.migareferenceformchange.latitude_1 = $scope.origin.latitude;
            $scope.migareferenceformchange.longitude_1 =
              $scope.origin.longitude;
            break;
          case 2:
            $scope.migareferenceformchange.latitude_2 = $scope.origin.latitude;
            $scope.migareferenceformchange.longitude_2 =
              $scope.origin.longitude;
            break;
          case 3:
            $scope.migareferenceformchange.latitude_3 = $scope.origin.latitude;
            $scope.migareferenceformchange.longitude_3 =
              $scope.origin.longitude;
            break;
          case 4:
            $scope.migareferenceformchange.latitude_4 = $scope.origin.latitude;
            $scope.migareferenceformchange.longitude_4 =
              $scope.origin.longitude;
            break;
          case 5:
            $scope.migareferenceformchange.latitude_5 = $scope.origin.latitude;
            $scope.migareferenceformchange.longitude_5 =
              $scope.origin.longitude;
            break;
          case 6:
            $scope.migareferenceformchange.latitude_6 = $scope.origin.latitude;
            $scope.migareferenceformchange.longitude_6 =
              $scope.origin.longitude;
            break;
          case 7:
            $scope.migareferenceformchange.latitude_7 = $scope.origin.latitude;
            $scope.migareferenceformchange.longitude_7 =
              $scope.origin.longitude;
            break;
          case 8:
            $scope.migareferenceformchange.latitude_8 = $scope.origin.latitude;
            $scope.migareferenceformchange.longitude_8 =
              $scope.origin.longitude;
            break;
          case 9:
            $scope.migareferenceformchange.latitude_9 = $scope.origin.latitude;
            $scope.migareferenceformchange.longitude_9 =
              $scope.origin.longitude;
            break;
          case 10:
            $scope.migareferenceformchange.latitude_10 = $scope.origin.latitude;
            $scope.migareferenceformchange.longitude_10 =
              $scope.origin.longitude;
            break;
        }
      };
      $scope.saveJob = function () {
        $scope.is_loading = true;
        Migareference.addNewJob($scope.jobform)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.loadjobslist();
            setTimeout(function () {
              $scope.modal.hide();
              $scope.notefield.notes_content = "";
            }, 3000);
          })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.owneractiverNumber = function (active_number) {
        $scope.migareferenceformchange.owner_hot = active_number;
        $scope.active_class_1 = "inactive_pile";
        $scope.active_class_2 = "inactive_pile";
        $scope.active_class_3 = "inactive_pile";
        $scope.active_class_4 = "inactive_pile";
        $scope.active_class_5 = "inactive_pile";
        if (active_number == 1) {
          $scope.active_class_1 = "active_pile";
        } else if (active_number == 2) {
          $scope.active_class_2 = "active_pile";
        } else if (active_number == 3) {
          $scope.active_class_3 = "active_pile";
        } else if (active_number == 4) {
          $scope.active_class_4 = "active_pile";
        } else if (active_number == 5) {
          $scope.active_class_5 = "active_pile";
        }
      };

      // Function to add new Referrer
      $scope.addNewReferrer = function () { 
          console.log("add new referrer");     
          $scope.openModel("newuserformmodal.html");
          $scope.migareferenceformchange.agent_user_id = "";
      };

      // Function to handle user selection and set the model
      $scope.selectedReferrerName="";
      $scope.setReferrerModel = function(userId, userName) {
        $scope.migareferenceformchange.refreral_user_id = userId;
        $scope.selectedReferrerName = userName;
        $scope.user_list = false; // Hide the list after selection
        $scope.getAgent();  // Get the agent for the selected user
      };

      // Function to get the agent for the selected user
      $scope.getAgent = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getAgent($scope.migareferenceformchange.refreral_user_id)
          .success(function (data) {
            console.log(data);
            if (data.agent_id) {
              $scope.migareferenceformchange.agent_user_id = data.agent_id;
            } else {
              $scope.migareferenceformchange.agent_user_id = "";
            }
          })
          .error(function () {
            $scope.is_loading = false;
            Loader.hide();
          })
          .finally(function () {
            setTimeout(function () {
              $scope.is_loading = false;
              Loader.hide();
            }, 1000);
          });
      };

      $scope.saveNewUser = function () {
        
        $scope.is_loading = true;
        Migareference.saveNewUser($scope.migareferencenewuser)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
           
            setTimeout(function () {
              $scope.modal.hide();
            }, 1000);
            $scope.migareferenceformchange.refreral_user_id =
              data.user_id + "@" + "1";
              $scope.modal.hide();
              $scope.reportsettings(1);
          })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
            
          });
      };
      $scope.addUser = function () {
        $scope.dobmodal.hide();
        $scope.saveNewUser();
      };
      $scope.goToHomePage = function () {
        $state.go("home");
      };
      $scope.goToMainMenu = function () {
        $state.go("migareference-view", {
          value_id: $stateParams.value_id,
        });
      };
      $scope.hideNewUserModal = function () {
        console.log($scope.modal);
        $scope.modal.hide();
      };
      $scope.openModel = function (modelName) {
        Modal.fromTemplateUrl(modelName, {
          scope: $scope,
        }).then(function (modal) {
          if (modelName == "newjobmodel.html") {
            if ($scope.migareferencenewuser.job_id == -1) {
              $scope.modal = modal;
              $scope.modal.show();
            }
          } else if (modelName == "confirmdob.html") {
            //check if DOB is enabled and filled or not
            if (
              $scope.pre_settings.mandatory_birthdate == 1 && $scope.pre_settings.enable_birthdate==1 && ( 
              $scope.migareferencenewuser.birth_day == 0 ||
              $scope.migareferencenewuser.birth_month == 0 ||
              $scope.migareferencenewuser.birth_year == 0)
          ){
              console.log("ready to open modal IF");
              $scope.dobmodal = modal;
              $scope.dobmodal.show();
            }else{
              $scope.saveNewUser();
            }
          } else {
            $scope.modal = modal;
            $scope.modal.show();
          }
        });
      };
      $scope.openPanel = function () {
        Modal.fromTemplateUrl("maps-info.html", {
          scope: $scope,
        }).then(function (modal) {
          $scope.panel = modal;
          $scope.alrt_container = 1;
          $scope.panel.show();
        });
      };
      $scope.showWarrning = function () {
        if ($scope.owner_warrning == 0) {
          Dialog.alert(
            $translate.instant("Confirm"),
            $translate.instant(
              "Before sending a contact, it is mandatory that you have spoken to him personally and that he is informed that he will receive a call from the real estate Agent."
            ),
            $translate.instant("I CONFIRM")
          );
          $scope.owner_warrning = 1;
        }
      };
      $scope.closePanel = function () {
        $scope.panel.remove();
        $scope.alrt_container = 0;
      };
      //START: Worked On Locations
      // Temp Location
      var address_model = "";
      $scope.setupaddressmodel = function (model) {
        var address_model = model;
      };
      $scope.loadHomecontent = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadHomecontent()
          .success(function (data) {
            $scope.home_icos = data.app_content;
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

      $scope.loadprovinces = function (item) {
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadprovinces(item.id)
          .success(function (data) {
            $scope.proviceitems = data.proviceitems;
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
      $scope.reportsettings = function (open_report_type) {
        $scope.is_loading = true;
        Loader.show();
        Migareference.reportsettings(Customer.customer.id, 2, 1)
          .success(function (data) {
            $scope.countryitems = data.geoCountries;
            $scope.proviceitems = data.proviceitems;
            $scope.pre_settings = data.pre_settings;
            $scope.gdpr_settings = data.gdpr_settings;
            country_modal = data.default_model[0];
            province_modal = data.default_model[1];
            // Setup Defaulkt values for fileds having defaulkt values
            for (const [key, value] of Object.entries(data.default_model)) {
              valueIndex_optionType = value.split("@");
              index = valueIndex_optionType[0];
              options_type = valueIndex_optionType[1];
              if (key >= 0) {
                switch (key) {
                  case "1":
                    if (options_type == 1) {
                      $scope.migareferenceformchange.extra_3 =
                        data.geoCountries[index];
                    } else if ((options_type = 2)) {
                      $scope.migareferenceformchange.extra_4 =
                        data.proviceitems[index];
                    }
                    break;
                  case "2":
                    if (options_type == 1) {
                      $scope.migareferenceformchange.extra_3 =
                        data.geoCountries[index];
                    } else if ((options_type = 2)) {
                      $scope.migareferenceformchange.extra_4 =
                        data.proviceitems[index];
                    }
                    break;
                  case "3":
                    if (options_type == 1) {
                      $scope.migareferenceformchange.extra_3 =
                        data.geoCountries[index];
                    } else if ((options_type = 2)) {
                      $scope.migareferenceformchange.extra_4 =
                        data.proviceitems[index];
                    }
                    break;
                  case "4":
                    if (options_type == 1) {
                      $scope.migareferenceformchange.extra_3 =
                        data.geoCountries[index];
                    } else if ((options_type = 2)) {
                      $scope.migareferenceformchange.extra_4 =
                        data.proviceitems[index];
                    }
                    break;
                  case "5":
                    if (options_type == 1) {
                      $scope.migareferenceformchange.extra_3 =
                        data.geoCountries[index];
                    } else if ((options_type = 2)) {
                      $scope.migareferenceformchange.extra_4 =
                        data.proviceitems[index];
                    }
                    break;
                  case "6":
                    if (options_type == 1) {
                      $scope.migareferenceformchange.extra_3 =
                        data.geoCountries[index];
                    } else if ((options_type = 2)) {
                      $scope.migareferenceformchange.extra_4 =
                        data.proviceitems[index];
                    }
                    break;
                  case "7":
                    if (options_type == 1) {
                      $scope.migareferenceformchange.extra_3 =
                        data.geoCountries[index];
                    } else if ((options_type = 2)) {
                      $scope.migareferenceformchange.extra_4 =
                        data.proviceitems[index];
                    }
                    break;
                  case "8":
                    if (options_type == 1) {
                      $scope.migareferenceformchange.extra_3 =
                        data.geoCountries[index];
                    } else if ((options_type = 2)) {
                      $scope.migareferenceformchange.extra_4 =
                        data.proviceitems[index];
                    }
                    break;
                  case "9":
                    if (options_type == 1) {
                      $scope.migareferenceformchange.extra_3 =
                        data.geoCountries[index];
                    } else if ((options_type = 2)) {
                      $scope.migareferenceformchange.extra_4 =
                        data.proviceitems[index];
                    }
                    break;
                  case "10":
                    if (options_type == 1) {
                      $scope.migareferenceformchange.extra_3 =
                        data.geoCountries[index];
                    } else if ((options_type = 2)) {
                      $scope.migareferenceformchange.extra_4 =
                        data.proviceitems[index];
                    }
                    break;
                }
              }
            }

            $scope.form_builder = $sce.trustAsHtml(data.form_builder);
            setTimeout(function () {
              $scope.tem_lable = "Time out";
            }, 3000);
            if (data.pre_settings.is_visible_invite_prospectus == 1) {
              $scope.is_visible_invite_prospectus = true;
            }
            if (data.pre_settings.internal_report_note != "") {
              $scope.is_internal_report_alert = true;
              $scope.internal_report_note = $sce.trustAsHtml(
                data.pre_settings.internal_report_note
              );
            }
            if (
              data.pre_settings.external_report_note != "" &&
              $scope.is_visible_invite_prospectus == true
            ) {
              $scope.is_external_report_alert = true;
              $scope.external_report_note = $sce.trustAsHtml(
                data.pre_settings.external_report_note
              );
            }
            if (data.pre_settings.is_visible_submit_report == 1) {
              if (ionic.Platform.isIOS()) {
                if (
                  data.pre_settings.is_visible_platform_report == 1 ||
                  data.pre_settings.is_visible_platform_report == 2
                ) {
                  $scope.is_visible_submit_report = true;
                  $scope.tem_text =
                    "IOS" + data.pre_settings.is_visible_platform_report;
                }
              } else if (ionic.Platform.isAndroid()) {
                if (
                  data.pre_settings.is_visible_platform_report == 1 ||
                  data.pre_settings.is_visible_platform_report == 3
                ) {
                  $scope.is_visible_submit_report = true;
                  $scope.tem_text =
                    "Android" + data.pre_settings.is_visible_platform_report;
                }
              } else {
                $scope.is_visible_submit_report = true;
                $scope.tem_text =
                  "Both" + data.pre_settings.is_visible_platform_report;
              }
            }
            // Collect Report Type
            if ($scope.pre_settings.enable_report_type==1 && !open_report_type) {
              Modal.fromTemplateUrl("reporttype.html", {
                scope: $scope,
              }).then(function (modal) {
                $scope.modal = modal;
                $scope.modal.show();
              });
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
      // End Location
      $scope.loadjobslist = function () {
        console.log("Called");
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadjobs()
          .success(function (data) {
            $scope.jobs_list = data.jobscollection;
            $scope.professions_list = data.professionscollection;
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
      $scope.user_list=false;
      $scope.showusrlist = function () {
        console.log("Show User List");
        $scope.searchTerm = '';
        $scope.user_list=!$scope.user_list;
      };
      $scope.loadHomecontent();
      $scope.loadjobslist();
      $scope.reportsettings(0);
    }
  );
