angular
  .module("starter")
  .controller(
    "MigareferencesubmitreportsController",
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
      $window,
      SocialSharing,
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
        internal_report_note: "",
        external_report_note: "",
        add_property_cover_file: "",
        tem_text: "",
        invite_message: "",
        origin: {
          address: null,
          latitude: null,
          longitude: null,
        },
      });
      $scope.countryitems = [];
      $scope.pre_settings = [];
      $scope.proviceitems = [];
      $scope.social_report_type=1;
      $scope.socialsharemessage=''
      $scope.daysdateday = [
        {
          id: "",
          name: $translate.instant("Day"),
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
      $scope.migareferencenewuser = {};
      $scope.migareferenceformchange = {
        user_id: Customer.customer.id,
        address: "",
        longitude: "",
        latitude: "",
        owner_hot: 3,
        owner_mobile: "+39",
        report_type: 1,
        report_custom_type:1
      };
      // $scope.shareSocial = function () {
      //   if ($scope.gdpr_settings.invite_consent_warning_active == 1) {
      //     Modal.fromTemplateUrl("invitegdprwarning.html", {
      //       scope: $scope,
      //     }).then(function (modal) {
      //       $scope.modal = modal;
      //       $scope.modal.show();
      //     });
      //   } else {
      //     $scope.shareInvite();
      //   }
      // };      
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
      // $scope.invitegdprwarrning = function (response) {
      //   $scope.is_loading = true;
      //   $scope.modal.hide();
      //   if (response) {
      //     $scope.shareInvite();
      //   }
      // };

      // $scope.shareInvite = function () {
      //     $scope.buildmessage($scope.migareferenceformchange.report_custom_type);
      // };
      // $scope.setSocialReportType = function (report_type) {          
      //     $scope.modal.hide();
      //     $scope.buildmessage(report_type);          
      // };
      $scope.buildmessage= function (share_type) {
        $scope.is_loading = true;
        console.log("Ready to build message");
        Migareference.buildmessage(
          Customer.customer.id,
          $scope.invo_settings.sponsor_id,
          Customer.customer.id,
          1,
          $scope.migareferenceformchange.report_custom_type
        )
          .success(function (data) {            
            $scope.socialsharemessage=data.invite_message;            
            if (!$rootScope.isNativeApp) {
              $scope.is_native=false;
            }else{
              $scope.is_native=true;
            }
            if ($scope.socialsharemessage == "") {
              Dialog.alert(
                $translate.instant("Error"),
                $translate.instant("Something went wrong, Please try again later!"),
                "OK"
              );
              return true;
            }    
            $scope.socialShare(share_type,$scope.socialsharemessage);
            // Modal.fromTemplateUrl("socialsharemessage.html", {
            //   scope: $scope,
            // }).then(function (modal) {
            //   $scope.modal = modal;
            //   $scope.modal.show();           
            // });          
          })
          .error(function (data) {
            $scope.disablereportSubmit = false;
            Dialog.alert(
              $translate.instant("Warning"),
              data.message,
              "OK"
            );
          })
          .finally(function () {
            
          });
      }
      $scope.shareSocialMessage = function (share_type,share_action_type) {
        if (share_type==1) { //Share          
        if (!$rootScope.isNativeApp) {
          Dialog.alert(
            $translate.instant("Error"),
            $translate.instant("This feature is disable on WebView!"),
            "OK"
          );
          return true;
        }
        $scope.saveSharelogs();
        return window.plugins.socialsharing.share($scope.socialsharemessage);        
        }else{ 
          // share_action_type (whatsapp,Email,SMS,copy,cancel)
          $scope.socialShare(share_action_type,$scope.socialsharemessage);
       
        }   
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
      $scope.shareConsentSocial = function (share_action_type) {
        $scope.socialShare(share_action_type,$scope.consent_invit_msg_body);
        $scope.modal.hide();
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
      $scope.saveSharelogs = function () {
        $scope.is_loading = true;
        Migareference.saveSharelogs(Customer.customer.id)
          .success(function (data) {
            console.log(data);
            console.log("Landing log saved");
          })
          .error(function (data) {
            console.log(data);
            console.log("Log error");
          });
      };
      $scope.submitReport=function () {
        Migareference.savePropertyreport($scope.migareferenceformchange)
          .success(function (data) {
            // Success Message
            Dialog.alert("Success", data.message, "OK");          
            // Consent Collection
            $scope.consent_invit_msg_body = data.consent_invit_msg_body;
            $scope.migareferenceformchange = {
              user_id: Customer.customer.id,
              address: "",
              longitude: "",
              latitude: "",
              owner_hot: 3,
            };            
            if ($scope.gdpr_settings.consent_info_active == 1) {
              setTimeout(function () {
                Modal.fromTemplateUrl("consentcollection.html", {
                  scope: $scope,
                }).then(function (modal) {
                  $scope.modal = modal;
                  $scope.modal.show();
                });
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
      $scope.addrefferaluser = function () {
        if ($scope.migareferenceformchange.refreral_user_id == 0) {
          Modal.fromTemplateUrl("newuserformmodal.html", {
            scope: $scope,
          }).then(function (modal) {
            $scope.modal = modal;
            $scope.modal.show();
          });
        }
      };
      $scope.saveNewUser = function () {
        $scope.is_loading = true;
        Migareference.saveNewUser($scope.migareferencenewuser)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.reportsettings();
            setTimeout(function () {
              $scope.modal.hide();
            }, 1000);
            $scope.migareferenceformchange.refreral_user_id =
              data.user_id + "@" + "1";
          })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.goToHomePage = function () {
        $state.go("home");
      };
      $scope.goToMainMenu = function () {
        $state.go("migareference-view", {
          value_id: $stateParams.value_id,
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
            $scope.add_property_cover_file = $sce.trustAsHtml(
              data.app_content.add_property_cover_file
            );
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

      $scope.reportsettings = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.reportsettings(Customer.customer.id, 1, 1)
          .success(function (data) {
            $scope.pre_settings = data.pre_settings;
            $scope.gdpr_settings = data.gdpr_settings;
            $scope.invo_settings = data.invo_settings;
            $scope.form_builder = $sce.trustAsHtml(data.form_builder);
            $scope.countryitems = data.geoCountries;
            $scope.proviceitems = data.proviceitems;
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
            setTimeout(function () {
              $scope.tem_lable = "Time out";
            }, 3000);
            if (data.pre_settings.is_visible_invite_prospectus == 1) {
              $scope.is_visible_invite_prospectus = true;
              // Prepare Link
              Migareference.buildmessage(
                Customer.customer.id,
                $scope.invo_settings.sponsor_id,
                Customer.customer.id,
                1
              )
                .success(function (data_build) {
                  //1 Referrer
                  var raw_message = data.gdpr_settings.invite_message + " ";
                  if (data_build.data.is_allow_socialshare == 2) {
                    $scope.is_visible_invite_prospectus = false;
                  }
                  raw_message = raw_message.replace(
                    "@@landing_link@@",
                    data_build.data.app_url
                  );
                  // PATCH: its a hard coded replacement of "@@referrer_name@@" from server side for compatiblity
                  raw_message = raw_message.replace(
                    "@@agent_name@@",
                    data_build.data.agent_data.firstname +
                      " " +
                      data_build.data.agent_data.lastname
                  );                                    
                  $scope.invite_message = raw_message;
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
            if ($scope.pre_settings.enable_report_type==1) {
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
      $scope.loadHomecontent();
      $scope.reportsettings();
    }
  );
