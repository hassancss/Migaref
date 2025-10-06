angular
  .module("starter")
  .controller(
    "MigareferencereportdetailController",
    function (
      Loader,
      GoogleMaps,
      $scope,
      $rootScope,
      $state,
      $stateParams,
      Customer,
      $translate,
      Migareference,
      Dialog,
      Modal,
      $ionicPopup,
      LinkService,
      $sce
    ) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
        getPropertysettings: null,
        owner_warrning: 0,
        page_title: "Report Detail",
        commission_title: $translate.instant("Commission Fee"),
        origin: {
          address: null,
          latitude: null,
          longitude: null,
        },
      });
      $scope.countryitems = [];
      $scope.proviceitems = [];
      $scope.reports = null;
      $scope.stasuses = null;
      $scope.home_icos = null;
      $scope.set_commission_fee = false;
      $scope.is_comment = false;
      $scope.notes = null;
      $scope.can_access_paid_status = true;
      $scope.tabs_css = "";
      $scope.tabs_back = "";
      $scope.tabs_back_detail = "";
      $scope.bottom_button = "";
      $scope.form_builder = "";

      $scope.notefield = {
        report_id: $stateParams.report_id,
        user_id: Customer.customer.id,
        operation: "create",
        public_key: 0,
      };
      $scope.reminderfields = {
        report_id: $stateParams.report_id,
        user_id: Customer.customer.id,
        operation: "create",
        public_key: 0,
      };
      // Custom Tabs
      $scope.tab1_css = "active_tab";
      $scope.tab2_css = "inactive_tab";
      $scope.tab3_css = "inactive_tab";
      $scope.show_tab1_content = true;
      $scope.show_tab2_content = false;
      $scope.show_tab3_content = false;
      $scope.tabs_shift = function (tab_key) {
        if (tab_key == 1) {
          $scope.show_tab1_content = true;
          $scope.show_tab2_content = false;
          $scope.show_tab3_content = false;
          $scope.tab1_css = "active_tab";
          $scope.tab2_css = "inactive_tab";
          $scope.tab3_css = "inactive_tab";
        } else if (tab_key == 2) {
          $scope.show_tab1_content = false;
          $scope.show_tab2_content = true;
          $scope.show_tab3_content = false;
          $scope.tab1_css = "inactive_tab";
          $scope.tab2_css = "active_tab";
          $scope.tab3_css = "inactive_tab";
        } else if (tab_key) {
          $scope.show_tab1_content = false;
          $scope.show_tab2_content = false;
          $scope.show_tab3_content = true;
          $scope.tab1_css = "inactive_tab";
          $scope.tab2_css = "inactive_tab";
          $scope.tab3_css = "active_tab";
        }
      };
      $scope.active_class_1 = "inactive_pile";
      $scope.active_class_2 = "inactive_pile";
      $scope.active_class_3 = "inactive_pile";
      $scope.active_class_4 = "inactive_pile";
      $scope.active_class_5 = "inactive_pile";
      $scope.previous_report_status = 0;
      $scope.migareferenceformchange = {};
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
      $scope.minutes = [
        {
          id: "",
          name: $translate.instant("Minutes"),
        },
      ];
      for (d = 0; d < 60; d++) {
        c = d;
        if (d < 10) {
          c = "0" + d;
        }
        $scope.minutes.push({ id: c, name: d });
      }
      $scope.hours = [
        {
          id: "",
          name: $translate.instant("Hours"),
        },
      ];
      for (d = 0; d <= 23; d++) {
        c = d;
        if (d < 10) {
          c = "0" + d;
        }
        $scope.hours.push({ id: c, name: d });
      }
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
      $scope.reminder_before_types = [
        {
          id: "01",
          name: $translate.instant("Just in Time"),
        },
        {
          id: "02",
          name: $translate.instant("before 15 mins"),
        },
        {
          id: "03",
          name: $translate.instant("before 30 mins"),
        },
        {
          id: "04",
          name: $translate.instant("before 45 mins"),
        },
        {
          id: "05",
          name: $translate.instant("before 1 hour"),
        },
        {
          id: "06",
          name: $translate.instant("before 2 hours"),
        },
        {
          id: "07",
          name: $translate.instant("before 6 hours"),
        },
        {
          id: "08",
          name: $translate.instant("before 1 day"),
        },
      ];
      // DOB Years
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
      // Reminder Years
      $scope.max_year = $scope.currunt_year + 12;
      $scope.reminder_years = [
        {
          id: "",
          name: $translate.instant("Year"),
        },
      ];
      for (y = $scope.currunt_year; y <= $scope.max_year; y++) {
        $scope.reminder_years.push({ id: y, name: y });
      }
      // Go to Home Page
      $scope.goToHomePage = function () {
        $state.go("home");
      };
      // Go to Show Reports Page
      $scope.discard = function () {
        $state.go("show-reportsv2", {
          value_id: $stateParams.value_id,
        });
      };
      $scope.phoneDetail = function (id) {
        //phontype:(1 Referrer, 2 Prospect)
        $state.go("phonedetail-listv2", {
          value_id: $stateParams.value_id,
          id: id,
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
      $scope.loadHomecontent();
      $scope.phoneCall = function (callTo) {   
        console.log("Phone call"+callTo);  
        callTo = String(callTo);   
        if (callTo.slice(0, 1) != "+" && callTo.slice(0, 2) != "00") {
          callTo = "+39" + callTo;
        }        
        LinkService.openLink("tel:" + callTo, {}, true);
      };
      $scope.whatsappCall = function (callTo) {        
        if (callTo.slice(0, 1) != "+" && callTo.slice(0, 2) != "00") {
          callTo = "+39" + callTo;
        }
        urltrigger = "https://api.whatsapp.com/send?phone=" + callTo;
        window.open(urltrigger, "_system", "location=yes");
      };
      $scope.changeItinerary = function () {
        $scope.migareferenceformchange.latitude = $scope.origin.latitude;
        $scope.migareferenceformchange.longitude = $scope.origin.longitude;
        $scope.setupMap(
          $scope.migareferenceformchange.longitude,
          $scope.migareferenceformchange.latitude
        );
        $scope.$apply();
      };
       //Status Change Validation
       $scope.status_change = function () {
        $scope.is_loading = true;
        Loader.show();
        report_id = $scope.migareferenceformchange.migareference_report_id;
        Migareference.chrckMandate(
          $scope.migareferenceformchange.currunt_report_status,
          report_id
        )
          .success(function (data) {
            $scope.migareferenceformchange.is_acquired =
              data.status_data.is_acquired;
            $scope.migareferenceformchange.new_order_id =
              data.status_data.order_id;
            if (data.report.commission_type == 1 || data.report.commission_type == 3) { // Percen Commission or Range Commission
              if (data.report.commission_fee > 0 && data.status_data.is_acquired == 0) {
                $scope.set_commission_fee = true;
                $scope.set_commission_fee = data.report.commission_fee;
                $scope.commission_fee_disabled = true;
              } else if (data.status_data.is_acquired == 1) {
                $scope.set_commission_fee = true;
                $scope.set_commission_fee = data.report.commission_fee;
                $scope.commission_fee_disabled = false;
              } else {
                $scope.set_commission_fee = false;
                $scope.set_commission_fee = data.report.commission_fee;
                $scope.commission_fee_disabled = true;
              }
            } else { //Fixed Commission
              $scope.set_commission_fee = true;
              $scope.set_commission_fee = data.report.commission_fee;
              $scope.commission_fee_disabled = true;
            }
            if (
              data.status_data.is_comment == 1 ||
              data.status_data.status_title == "Declinato/Non Venduto"
            ) {
              $scope.is_comment = true;
              $scope.migareferenceformchange.comment_required = 1;
            } else {
              $scope.is_comment = false;
              $scope.migareferenceformchange.comment_required = 0;
            }
            $scope.migareferenceformchange.standard_type =
              data.status_data.standard_type;
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
      // NOTES
      $scope.createnote = function () {
        $scope.notesformmodal();
        $scope.notefield.operation = "create";
      };
      $scope.notesformmodal = function () {
        Modal.fromTemplateUrl("notesformmodal.html", {
          scope: $scope,
        }).then(function (modal) {
          $scope.modal = modal;
          $scope.modal.show();
        });
      };
      $scope.saveNote = function () {
        $scope.is_loading = true;
        Migareference.saveNotes($scope.notefield)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.getnoteslist();
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
      $scope.getnoteslist = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getnoteslist($stateParams.report_id)
          .success(function (data) {
            $scope.notescollection = data.notes;
            console.log(ionic.Platform);
            if (ionic.Platform.isIOS()) {
              $scope.tabs_css = data.ios_tabs;
              $scope.tabs_back_detail = data.ios_back_detail;
              $scope.tabs_back = data.ios_back;
              $scope.bottom_button = data.ios_bottom;
            } else if (ionic.Platform.isAndroid()) {
              $scope.tabs_css = data.android_tabs;
              $scope.tabs_back_detail = data.android_back_detail;
              $scope.tabs_back = data.android_back;
              $scope.bottom_button = data.android_bottom;
            } else {
              $scope.tabs_css = data.other_tabs;
              $scope.tabs_back_detail = data.other_back_detail;
              $scope.tabs_back = data.other_back;
              $scope.bottom_button = data.other_bottom;
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
      $scope.editNote = function (public_key) {
        $scope.notesformmodal();
        $scope.notefield.operation = "update";
        $scope.is_loading = true;
        Loader.show();
        Migareference.editNote(public_key)
          .success(function (data) {
            $scope.notefield.public_key = data.note.migarefrence_notes_id;
            $scope.notefield.notes_content = data.note.notes_content;
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
      $scope.deleteNote = function (public_key) {
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template:
            $translate.instant("Are you sure you want to delete this record") +
            "?",
          cancelText: "No",
          okText: $translate.instant("Yes"),
        });
        confirmPopup.then(function (res) {
          if (res) {
            $scope.is_loading = true;
            Loader.show();
            Migareference.deleteNote(public_key)
              .success(function (data) {
                Dialog.alert("Success", data.message, "OK");
                $scope.getnoteslist();
              })
              .error(function () {
                $scope.is_loading = false;
                Loader.hide();
              })
              .finally(function () {
                $scope.is_loading = false;
                Loader.hide();
              });
          }
        });
      };
      // REMINDER
      $scope.reminderformmodal = function () {
        Modal.fromTemplateUrl("reminderformmodal.html", {
          scope: $scope,
        }).then(function (modal) {
          $scope.modal = modal;
          $scope.modal.show();
        });
      };
      $scope.createreminder = function () {
        $scope.reminderformmodal();
        $scope.reminderfields = {
          report_id: $stateParams.report_id,
          user_id: Customer.customer.id,
          operation: "create",
          public_key: 0,
        };
      };
      $scope.getreportremindertype = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getreportremindertype()
          .success(function (data) {
            $scope.remindertypecollection = data.remindertypecollection;
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
      $scope.saveReminder = function () {
        $scope.is_loading = true;
        Migareference.saveReminder($scope.reminderfields)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.getreminderlist();
            $scope.reminderfields = {
              report_id: $stateParams.report_id,
              user_id: Customer.customer.id,
              operation: "create",
              public_key: 0,
            };
            setTimeout(function () {
              $scope.modal.hide();
            }, 3000);
          })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.getreminderlist = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getreminderlist($stateParams.report_id)
          .success(function (data) {
            $scope.remindercollection = data.remindercollection;
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
      $scope.editReminder = function (public_key) {
        $scope.reminderformmodal();
        $scope.reminderfields = {
          report_id: $stateParams.report_id,
          user_id: Customer.customer.id,
          operation: "update",
          public_key: 0,
        };
        $scope.is_loading = true;
        Loader.show();
        Migareference.editReminder(public_key)
          .success(function (data) {
            $scope.reminderfields.public_key =
              data.reminder.migarefrence_reminders_id;
            $scope.reminderfields.event_type = data.reminder.event_type;
            $scope.reminderfields.event_day = (
              "0" + data.reminder.event_day
            ).slice(-2);
            $scope.reminderfields.event_month = (
              "0" + data.reminder.event_month
            ).slice(-2);
            $scope.reminderfields.event_year = data.reminder.event_year;
            $scope.reminderfields.event_hour = (
              "0" + data.reminder.event_hour
            ).slice(-2);
            $scope.reminderfields.event_min = (
              "0" + data.reminder.event_min
            ).slice(-2);
            $scope.reminderfields.reminder_before_type = (
              "0" + data.reminder.reminder_before_type
            ).slice(-2);
            $scope.reminderfields.reminder_content =
              data.reminder.reminder_content;
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
      $scope.deleteReminder = function (public_key) {
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template:
            $translate.instant("Are you sure you want to delete this record") +
            "?",
          cancelText: "No",
          okText: $translate.instant("Yes"),
        });
        confirmPopup.then(function (res) {
          if (res) {
            $scope.is_loading = true;
            Loader.show();
            Migareference.deleteReminder(public_key)
              .success(function (data) {
                Dialog.alert("Success", data.message, "OK");
                $scope.getreminderlist();
              })
              .error(function () {
                $scope.is_loading = false;
                Loader.hide();
              })
              .finally(function () {
                $scope.is_loading = false;
                Loader.hide();
              });
          }
        });
      };
      $scope.getreminderlist();
      $scope.getreportremindertype();
      // Loade Report Detail
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
      $scope.shareConsent = function (consent_invit_msg_body) {
        if (consent_invit_msg_body == "") {
          return true;
        }
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
        return window.plugins.socialsharing.share(consent_invit_msg_body);
      };
      $scope.downloadCertificate = function (report_id) {
        LinkService.openLink(
          BASE_URL +
            "/migareference/public_pdf/download-pdf/report_id/" +
            report_id,
          {},
          true
        );
      };
      $scope.loadeReportdata = function (report_id) {
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadeReportdata(report_id, Customer.customer.id)
          .success(function (data) {
            $scope.form_builder = $sce.trustAsHtml(data.form_builder);
            $scope.countryitems = data.geoCountries;
            $scope.margin_setting = data.margin_setting;
            $scope.whatsapp_icon = data.whatsapp_icon;
            $scope.proviceitems = data.proviceitems;
            $scope.enable_gdpr = data.enable_gdpr;
            $scope.can_access_paid_status = data.can_access_paid_status !== false;
            country_modal = data.default_model[0];
            province_modal = data.default_model[1];
            $scope.reports = data;
            $scope.stasuses = data.status;
            $scope.block_icon = data.all_reports[0].block_icon;
            $scope.migareferenceformchange.migareference_report_id =
              data.all_reports[0].migareference_report_id;
            $scope.migareferenceformchange.sales_expectations = parseInt(
              data.all_reports[0].sales_expectations
            );
            $scope.migareferenceformchange.currunt_report_status =
              data.all_reports[0].migareference_report_status_id;
            $scope.previous_report_status =
              data.all_reports[0].report_status_id;
            $scope.migareferenceformchange.commission_fee =
              data.all_reports[0].commission_fee;
            $scope.migareferenceformchange.commission_fee_report =
              data.all_reports[0].commission_fee;
            $scope.migareferenceformchange.acquired =
              data.all_reports[0].acquired;
            $scope.migareferenceformchange.comment =
              data.all_reports[0].comment;
            $scope.migareferenceformchange.reward_type =
              data.all_reports[0].reward_type;
            $scope.migareferenceformchange.owner_name =
              data.all_reports[0].owner_name;
            $scope.migareferenceformchange.owner_surname =
              data.all_reports[0].owner_surname;
            $scope.migareferenceformchange.note = data.all_reports[0].note;
            $scope.migareferenceformchange.owner_mobile =
              data.all_reports[0].owner_mobile;
            $scope.migareferenceformchange.commission_type =
              data.all_reports[0].commission_type;
            $scope.migareferenceformchange.order_id =
              data.all_reports[0].order_id;
            $scope.migareferenceformchange.new_order_id =
              data.all_reports[0].order_id;
            $scope.migareferenceformchange.user_id = Customer.customer.id;
            $scope.migareferenceformchange.owner_hot =
              data.all_reports[0].owner_hot;
            $scope.migareferenceformchange.standard_type =
              data.all_reports[0].standard_type;
            $scope.migareferenceformchange.report_custom_type =data.all_reports[0].report_custom_type;
            // Custome Filed settings handler
            $scope.migareferenceformchange.address =
              data.all_reports[0].address;
            $scope.migareferenceformchange.longitude =
              data.all_reports[0].longitude;
            $scope.migareferenceformchange.latitude =
              data.all_reports[0].latitude;
            $scope.migareferenceformchange.property_type =
              data.all_reports[0].property_type;
            if (data.all_reports[0].owner_dob != "") {
              dob = data.all_reports[0].owner_dob.split("-");
              $scope.migareferenceformchange.birth_year = dob[0];
              $scope.migareferenceformchange.birth_month = dob[1];
              $scope.migareferenceformchange.birth_day = dob[2];
            } else {
              $scope.migareferenceformchange.birth_year = 0;
              $scope.migareferenceformchange.birth_month = 0;
              $scope.migareferenceformchange.birth_day = 0;
            }
            // Field Values for Extra Fields
            country_id = 0;
            for (const [key, value] of Object.entries(
              data.all_reports[0].extra_dynamic_field_settings
            )) {
              if (value.type == 2) {
                switch (value.field_type_count) {
                  case "1":
                    set_value =
                      data.all_reports[0].extra_dynamic_fields.extra_1;
                    if (value.field_type == 2) {
                      set_value = parseInt(
                        data.all_reports[0].extra_dynamic_fields.extra_1
                      );
                    }
                    if (value.field_type == 4) {
                      $scope.migareferenceformchange.latitude_3 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                      $scope.migareferenceformchange.latitude_3 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                    }
                    $scope.migareferenceformchange.extra_1 = set_value;
                    // Cour COuntries na dProvince
                    if (value.field_type == 3 && value.options_type == 1) {
                      $scope.migareferenceformchange.extra_1 =
                        data.geoCountries[set_value];
                    }
                    if (value.field_type == 3 && value.options_type == 2) {
                      $scope.migareferenceformchange.extra_1 =
                        data.proviceitems[set_value];
                    }
                    break;
                  case "2":
                    set_value =
                      data.all_reports[0].extra_dynamic_fields.extra_2;
                    if (value.field_type == 2) {
                      set_value = parseInt(
                        data.all_reports[0].extra_dynamic_fields.extra_2
                      );
                    }
                    if (value.field_type == 4) {
                      $scope.migareferenceformchange.latitude_2 =
                        data.all_reports[0].extra_dynamic_fields.latitude_2;
                      $scope.migareferenceformchange.latitude_2 =
                        data.all_reports[0].extra_dynamic_fields.latitude_2;
                    }
                    $scope.migareferenceformchange.extra_2 = set_value;
                    // Cour COuntries na dProvince
                    if (value.field_type == 3 && value.options_type == 1) {
                      $scope.migareferenceformchange.extra_2 =
                        data.geoCountries[set_value];
                    }
                    if (value.field_type == 3 && value.options_type == 2) {
                      $scope.migareferenceformchange.extra_2 =
                        data.proviceitems[set_value];
                    }
                    break;
                  case "3":
                    set_value =
                      data.all_reports[0].extra_dynamic_fields.extra_3;
                    if (value.field_type == 2) {
                      set_value = parseInt(
                        data.all_reports[0].extra_dynamic_fields.extra_3
                      );
                    }
                    if (value.field_type == 4) {
                      $scope.migareferenceformchange.latitude_3 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                      $scope.migareferenceformchange.latitude_3 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                    }
                    $scope.migareferenceformchange.extra_3 = set_value;
                    // Cour COuntries na dProvince
                    if (value.field_type == 3 && value.options_type == 1) {
                      $scope.migareferenceformchange.extra_3 =
                        data.geoCountries[set_value];
                      country_id = data.geoCountries[set_value].id;
                    }
                    if (value.field_type == 3 && value.options_type == 2) {
                      $scope.migareferenceformchange.extra_3 =
                        data.proviceitems[set_value];
                    }
                    break;
                  case "4":
                    set_value =
                      data.all_reports[0].extra_dynamic_fields.extra_4;
                    if (value.field_type == 2) {
                      set_value = parseInt(
                        data.all_reports[0].extra_dynamic_fields.extra_4
                      );
                    }
                    if (value.field_type == 4) {
                      $scope.migareferenceformchange.latitude_4 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                      $scope.migareferenceformchange.latitude_4 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                    }
                    $scope.migareferenceformchange.extra_4 = set_value;
                    // Cour COuntries na dProvince
                    if (value.field_type == 3 && value.options_type == 1) {
                      $scope.migareferenceformchange.extra_4 =
                        data.geoCountries[set_value];
                    }
                    if (value.field_type == 3 && value.options_type == 2) {
                      $scope.migareferenceformchange.extra_4 =
                        data.proviceitems[set_value];
                    }
                    break;
                  case "5":
                    set_value =
                      data.all_reports[0].extra_dynamic_fields.extra_5;
                    if (value.field_type == 2) {
                      set_value = parseInt(
                        data.all_reports[0].extra_dynamic_fields.extra_5
                      );
                    }
                    if (value.field_type == 4) {
                      $scope.migareferenceformchange.latitude_5 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                      $scope.migareferenceformchange.latitude_5 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                    }
                    $scope.migareferenceformchange.extra_5 = set_value;
                    // Cour COuntries na dProvince
                    if (value.field_type == 3 && value.options_type == 1) {
                      $scope.migareferenceformchange.extra_5 =
                        data.geoCountries[set_value];
                    }
                    if (value.field_type == 3 && value.options_type == 2) {
                      $scope.migareferenceformchange.extra_5 =
                        data.proviceitems[set_value];
                    }
                    break;
                  case "6":
                    set_value =
                      data.all_reports[0].extra_dynamic_fields.extra_6;
                    if (value.field_type == 2) {
                      set_value = parseInt(
                        data.all_reports[0].extra_dynamic_fields.extra_6
                      );
                    }
                    if (value.field_type == 4) {
                      $scope.migareferenceformchange.latitude_6 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                      $scope.migareferenceformchange.latitude_6 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                    }
                    $scope.migareferenceformchange.extra_6 = set_value;
                    // Cour COuntries na dProvince
                    if (value.field_type == 3 && value.options_type == 1) {
                      $scope.migareferenceformchange.extra_6 =
                        data.geoCountries[set_value];
                    }
                    if (value.field_type == 3 && value.options_type == 2) {
                      $scope.migareferenceformchange.extra_6 =
                        data.proviceitems[set_value];
                    }
                    break;
                  case "7":
                    set_value =
                      data.all_reports[0].extra_dynamic_fields.extra_7;
                    if (value.field_type == 2) {
                      set_value = parseInt(
                        data.all_reports[0].extra_dynamic_fields.extra_7
                      );
                    }
                    if (value.field_type == 4) {
                      $scope.migareferenceformchange.latitude_7 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                      $scope.migareferenceformchange.latitude_7 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                    }
                    $scope.migareferenceformchange.extra_7 = set_value;
                    // Cour COuntries na dProvince
                    if (value.field_type == 3 && value.options_type == 1) {
                      $scope.migareferenceformchange.extra_7 =
                        data.geoCountries[set_value];
                    }
                    if (value.field_type == 3 && value.options_type == 2) {
                      $scope.migareferenceformchange.extra_7 =
                        data.proviceitems[set_value];
                    }
                    break;
                  case "8":
                    set_value =
                      data.all_reports[0].extra_dynamic_fields.extra_8;
                    if (value.field_type == 2) {
                      set_value = parseInt(
                        data.all_reports[0].extra_dynamic_fields.extra_8
                      );
                    }
                    if (value.field_type == 4) {
                      $scope.migareferenceformchange.latitude_8 =
                        data.all_reports[0].extra_dynamic_fields.latitude_4;
                      $scope.migareferenceformchange.latitude_8 =
                        data.all_reports[0].extra_dynamic_fields.latitude_4;
                    }
                    $scope.migareferenceformchange.extra_8 = set_value;
                    // Cour COuntries na dProvince
                    if (value.field_type == 3 && value.options_type == 1) {
                      $scope.migareferenceformchange.extra_8 =
                        data.geoCountries[set_value];
                    }
                    if (value.field_type == 3 && value.options_type == 2) {
                      $scope.migareferenceformchange.extra_8 =
                        data.proviceitems[set_value];
                    }
                    break;
                  case "9":
                    set_value =
                      data.all_reports[0].extra_dynamic_fields.extra_9;
                    if (value.field_type == 2) {
                      set_value = parseInt(
                        data.all_reports[0].extra_dynamic_fields.extra_9
                      );
                    }
                    if (value.field_type == 4) {
                      $scope.migareferenceformchange.latitude_9 =
                        data.all_reports[0].extra_dynamic_fields.latitude_5;
                      $scope.migareferenceformchange.latitude_9 =
                        data.all_reports[0].extra_dynamic_fields.latitude_5;
                    }
                    $scope.migareferenceformchange.extra_9 = set_value;
                    // Cour COuntries na dProvince
                    if (value.field_type == 3 && value.options_type == 1) {
                      $scope.migareferenceformchange.extra_9 =
                        data.geoCountries[set_value];
                    }
                    if (value.field_type == 3 && value.options_type == 2) {
                      $scope.migareferenceformchange.extra_9 =
                        data.proviceitems[set_value];
                    }
                    break;
                  case "10":
                    set_value =
                      data.all_reports[0].extra_dynamic_fields.extra_10;
                    if (value.field_type == 2) {
                      set_value = parseInt(
                        data.all_reports[0].extra_dynamic_fields.extra_10
                      );
                    }
                    if (value.field_type == 4) {
                      $scope.migareferenceformchange.latitude_10 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                      $scope.migareferenceformchange.latitude_10 =
                        data.all_reports[0].extra_dynamic_fields.latitude_3;
                    }
                    $scope.migareferenceformchange.extra_10 = set_value;
                    // Cour COuntries na dProvince
                    if (value.field_type == 3 && value.options_type == 1) {
                      $scope.migareferenceformchange.extra_10 =
                        data.geoCountries[set_value];
                    }
                    if (value.field_type == 3 && value.options_type == 2) {
                      $scope.migareferenceformchange.extra_10 =
                        data.proviceitems[set_value];
                    }
                    break;
                }
              }
            }
            if (data.all_reports[0].owner_hot == 1) {
              $scope.active_class_1 = "active_pile";
            } else if (data.all_reports[0].owner_hot == 2) {
              $scope.active_class_2 = "active_pile";
            } else if (data.all_reports[0].owner_hot == 3) {
              $scope.active_class_3 = "active_pile";
            } else if (data.all_reports[0].owner_hot == 4) {
              $scope.active_class_4 = "active_pile";
            } else if (data.all_reports[0].owner_hot == 5) {
              $scope.active_class_5 = "active_pile";
            }
            long = parseFloat($scope.migareferenceformchange.longitude);
            lat = parseFloat($scope.migareferenceformchange.latitude);
            // Commission Fee Secction
            if (data.all_reports[0].reward_type == 2) {
              $scope.commission_title = $translate.instant("Credits");
            }
            if (
              data.all_reports[0].commission_type == 1 ||
              data.all_reports[0].commission_type == 3
            ) {
              if (data.all_reports[0].commission_fee > 0) {
                $scope.set_commission_fee = true;
                $scope.commission_fee_disabled = true;
              } else if (data.all_reports[0].is_acquired == 1) {
                $scope.set_commission_fee = true;
                $scope.commission_fee_disabled = false;
              } else {
                $scope.set_commission_fee = false;
                $scope.commission_fee_disabled = true;
              }
            } else {
              $scope.set_commission_fee = true;
              $scope.commission_fee_disabled = true;
            }
            // Comment Section
            if (data.all_reports[0].comment != null) {
              $scope.is_comment = true;
              $scope.migareferenceformchange.comment_required = 1;
            } else {
              $scope.migareferenceformchange.comment_required = 0;
            }
            // Apply map if enabled
            // $scope.setupMap(long, lat);
            console.log("Final values");
            console.log($scope.migareferenceformchange);
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
      // Update Report Data
      $scope.updatePropertyreport = function () {
        $scope.is_loading = true;
        Migareference.updatePropertyreport($scope.migareferenceformchange)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            setTimeout(function () {
              $state.go("show-reportsv2", {
                value_id: $stateParams.value_id,
              });
            }, 3000);
          })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
        
     
      // Deafualt Functions Called
      $scope.loadeReportdata($stateParams.report_id);
      $scope.getnoteslist();
      //START: Worked On Locations
      $scope.initGoogleAutocomplete = function () {
        autocomplete = new google.maps.places.Autocomplete(
          document.getElementById("your_location"),
          { types: ["geocode"] }
        );
        google.maps.event.addListener(
          autocomplete,
          "place_changed",
          function () {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
              return;
            }
            $scope.user_latitude = place.geometry.location.lat();
            $scope.user_longitude = place.geometry.location.lng();
            $scope.has_location = true;
            $scope.migareferenceformchange.address = place.formatted_address;
            $scope.migareferenceformchange.latitude = $scope.user_latitude;
            $scope.migareferenceformchange.longitude = $scope.user_longitude;
            $scope.setupMap(
              $scope.migareferenceformchange.longitude,
              $scope.migareferenceformchange.latitude
            );
            $scope.$apply();
          }
        );
      };
      $scope.setupMap = function (longitude, latitude) {
        setTimeout(function () {
          GoogleMaps.init();
          $scope.initGoogleAutocomplete();
          var latLng = new google.maps.LatLng(longitude, latitude);
          var map, markerClusterer;
          var cobalt = [
            {
              featureType: "all",
              elementType: "all",
              stylers: [
                {
                  invert_lightness: true,
                },
                {
                  saturation: 10,
                },
                {
                  lightness: 30,
                },
                {
                  gamma: 0.5,
                },
                {
                  hue: "#435158",
                },
              ],
            },
          ];
          var style = {};
          var date = new Date();
          var hour = date.getHours();
          if (hour >= 18 || hour < 4) {
            style = cobalt;
          }
          map = new google.maps.Map(
            document.getElementById("appmetropolisv2-maps"),
            {
              center: {
                lat: latitude,
                lng: longitude,
              },
              zoom: 16,
              styles: style,
            }
          );
        }, 2000);
      };
      //END: Worked On Locations
    }
  );
