angular
  .module("starter")
  .controller(
    "MigareferencereminderlistController",
    function (
      Loader,
      $scope,
      $state,
      $stateParams,
      Customer,
      $translate,
      $rootScope,
      Migareference,
      Dialog,
      $ionicModal,
      Modal,
      $window,
      $sce,
      $ionicPopup,
      LinkService
    ) {
      angular.extend($scope, {
        is_loading: true,
        is_call_script_enabled: false,
        referrer_id: 0,
        value_id: $stateParams.value_id,
        prospectreminderfilter: { activefilter: "allactivestatus" },
        agentreminderfilter: {},
        autoagentreminderfilter: {},
        daybook_by_date:{},
        daybook_item:{},
        automationreminderfilter: { activefilter: "allactivestatus" },
        reminder_filter_1: "button-outline button-balanced",
        reminder_filter_2: "button-outline button-stable",
        reminder_filter_3: "button-outline button-stable",
        reminder_filter_4: "button-outline button-stable",
        reminder_filter_5: "button-outline button-stable",
        auto_reminder_filter_1: "button-outline button-balanced",
        auto_reminder_filter_2: "button-outline button-stable",
        auto_reminder_filter_3: "button-outline button-stable",
        auto_reminder_filter_4: "button-outline button-stable",
        auto_reminder_filter_5: "button-outline button-stable",
      });
      $scope.filters = {
        automation_agent_group_id: -1,
        apointment_agent_group_id: -1,
      };
      console.log("Here is app COntent");
      console.log(Migareference.app_content);
      $scope.app_content=Migareference.app_content;      
      $scope.loading_placeholder=$translate.instant("Loading")+'.....';
      $scope.is_loading_ref_rem=true;
      $scope.selectedIndex;
      $scope.selectedPotposneIndex;
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
        } else if(tab_key == 2) {
          console.log("2");
          $scope.show_tab1_content = false;
          $scope.show_tab2_content = true;
          $scope.show_tab3_content = false;
          $scope.tab1_css = "inactive_tab";
          $scope.tab2_css = "active_tab";
          $scope.tab3_css = "inactive_tab";
        }
        else{
          console.log("3");
          $scope.show_tab1_content = false;
          $scope.show_tab2_content = false;
          $scope.show_tab3_content = true;
          $scope.tab1_css = "inactive_tab";
          $scope.tab2_css = "inactive_tab";
          $scope.tab3_css = "active_tab";
          //Load day book data
          $scope.loadDaybook(Customer.customer.id);
        }
        console.log($scope.tab1_css);
        console.log($scope.tab2_css);
        console.log($scope.tab3_css);
      };

      $scope.openReport = function (report_id) {
        $state.go("report-detailv2", {
          value_id: $stateParams.value_id,
          report_id: report_id,
        });
      };
      $scope.editedItem = {};
      $scope.editedPotponeItem = {};
      $scope.editNote = function ($index) {
        $scope.selectedIndex = $index;
        $scope.$index = $index;
        angular.copy($scope.reminderlist[$index], $scope.editedItem);
      };
      $scope.saveNote = function () {
        angular.copy($scope.editedItem, $scope.reminderlist[$scope.$index]);
        localitem = $scope.reminderlist[$scope.$index];
        setReminder(
          localitem.reminder_id,
          localitem.report_id,
          "Notes",
          localitem.reminder_content
        );
      };
      $scope.portponeReminder = function ($index) {
        $scope.selectedPotposneIndex = $index;
        $scope.$index = $index;
        angular.copy($scope.reminderlist[$index], $scope.editedPotponeItem);
      };
      $scope.savePostpone = function () {
        angular.copy(
          $scope.editedPotponeItem,
          $scope.reminderlist[$scope.$index]
        );
        localitem = $scope.reminderlist[$scope.$index];
        setReminder(
          localitem.reminder_id,
          localitem.report_id,
          "Postpone",
          localitem.postpone_days
        );
      };

      $scope.canceleEditNote = function () {
        $scope.selectedIndex = -1;
      };
      $scope.cancelePostpone = function () {
        $scope.selectedPotposneIndex = -1;
      };
      // Auto Reminder Action
      $scope.editedAutoPotponeItem = {};
      $scope.editAutoNote = function ($index) {
        $scope.selectedAutoIndex = $index;
      };
      $scope.saveAutoNote = function (item) {
        setAutoReminder(
          item.reminder_id,
          "Notes",
          item.reminder_content,
          item.phobebook_id
        );
      };
      $scope.portponeAutoReminder = function ($index) {
        $scope.selectedAutoPotposneIndex = $index;
      };
      $scope.saveAutoPostpone = function (item) {
        setAutoReminder(
          item.reminder_id,
          "Postpone",
          item.postpone_days,
          item.phonebook_id
        );
      };

      $scope.canceleEditAutoNote = function () {
        $scope.selectedAutoIndex = -1;
      };
      $scope.canceleAutoPostpone = function () {
        $scope.selectedAutoPotposneIndex = -1;
      };
      // Formate PhooneNumber to International Standard
      //#IF the number is not starting with international code "+" or "00" we add by default +39
      $scope.call = function (item) {
        let callTo = item.referrer_mobile;
        if (callTo.slice(0, 1) != "+" && callTo.slice(0, 2) != "00") {
          callTo = "+39" + callTo;
        }
        LinkService.openLink("tel:" + callTo, {}, true);
      };
      $scope.mail = function (email) {
        $window.open("mailto:" + email, "_system");
      };
      $scope.whatsapp = function (callTo) {
        urltrigger = "https://api.whatsapp.com/send?phone=" + callTo;
        window.open(urltrigger, "_system", "location=yes");
      };
      $scope.loadDaybook = function (customer_id) {
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadDaybook(customer_id)
          .success(function (data) {
            $scope.daybook_by_date = data.day_book_collection;
            console.log(data);
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
      $scope.reminderformmodal = function () {
        Modal.fromTemplateUrl("postponereminder.html", {
          scope: $scope,
        }).then(function (modal) {
          $scope.modal = modal;
          $scope.modal.show();
        });
      };
      $scope.saveReminder = function (reminderitem) {
        $scope.is_loading = true;
        Migareference.saveReminder(reminderitem)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.getreportreminders(1);
          })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.updateAutoReminder = function (status, public_key, phonebook_id) {
        console.log("Firat Methodz");
        console.log("public_key@" + public_key);
        console.log("status@" + status);
        console.log("phonebook_id@" + phonebook_id);
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template:
            $translate.instant(
              "Are you sure you want to update this reminder"
            ) + "?",
          cancelText: "No",
          okText: $translate.instant("Yes"),
        });
        confirmPopup.then(function (res) {
          if (res) {
            $scope.is_loading = true;
            Loader.show();
            setAutoReminder(public_key, status, "", phonebook_id);
          }
        });
      };
      function setAutoReminder(public_key, status, changeValues, phonebook_id) {
        Migareference.updateAutoReminder(
          public_key,
          Customer.customer.id,
          status,
          changeValues,
          phonebook_id
        )
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.getreferrerreminders();
            $scope.selectedAutoIndex = -1;
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
      $scope.statusFilter = function (filterKey, activefilterkey) {
        $scope.prospectreminderfilter.status_filter = filterKey;
        $scope.prospectreminderfilter.activefilter = activefilterkey;
        $scope.reminder_filter_1 = "button-outline button-stable";
        $scope.reminder_filter_2 = "button-outline button-stable";
        $scope.reminder_filter_3 = "button-outline button-stable";
        $scope.reminder_filter_4 = "button-outline button-stable";
        $scope.reminder_filter_5 = "button-outline button-stable";
        switch (filterKey) {
          case "":
            $scope.reminder_filter_1 = "button-outline button-balanced";
            break;
          case "done":
            $scope.reminder_filter_2 = "button-outline button-balanced";
            break;
          case "pending":
            $scope.reminder_filter_3 = "button-outline button-balanced";
            break;
          case "postpone":
            $scope.reminder_filter_4 = "button-outline button-balanced";
            break;
          case "cancele":
            $scope.reminder_filter_5 = "button-outline button-balanced";
            break;
        }
      };
      $scope.autoStatusFilter = function (filterKey, activefilterkey) {
        $scope.automationreminderfilter.status_filter = filterKey;
        $scope.automationreminderfilter.activefilter = activefilterkey;
        $scope.auto_reminder_filter_1 = "button-outline button-stable";
        $scope.auto_reminder_filter_2 = "button-outline button-stable";
        $scope.auto_reminder_filter_3 = "button-outline button-stable";
        $scope.auto_reminder_filter_4 = "button-outline button-stable";
        $scope.auto_reminder_filter_5 = "button-outline button-stable";
        switch (filterKey) {
          case "":
            $scope.auto_reminder_filter_1 = "button-outline button-balanced";
            break;
          case "done":
            $scope.auto_reminder_filter_2 = "button-outline button-balanced";
            break;
          case "pending":
            $scope.auto_reminder_filter_3 = "button-outline button-balanced";
            break;
          case "postpone":
            $scope.auto_reminder_filter_4 = "button-outline button-balanced";
            break;
          case "cancele":
            $scope.auto_reminder_filter_5 = "button-outline button-balanced";
            break;
        }
      };
      $scope.saveReminder = function (reminderitem) {
        $scope.is_loading = true;
        Migareference.saveReminder(reminderitem)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.getreportreminders(1);
          })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.updateReminder = function (status, public_key, report_id) {
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template:
            $translate.instant("Are you sure you want to Done this reminder") +
            "?",
          cancelText: "No",
          okText: $translate.instant("Yes"),
        });
        confirmPopup.then(function (res) {
          if (res) {
            $scope.is_loading = true;
            Loader.show();
            setReminder(public_key, report_id, status, "");
          }
        });
      };
      function setReminder(public_key, report_id, status, changeValues) {
        Migareference.updateReminder(
          public_key,
          report_id,
          Customer.customer.id,
          status,
          changeValues
        )
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.getreportreminders(1);
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
      $scope.getreportreminders = function (key) {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getreportreminders(Customer.customer.id, key)
          .success(function (data) {
            $scope.statusFilter("", "allactivestatus");
            $scope.reminderlist = data.remindercollection;
            $scope.filteredreminderlist = data.remindercollection;            
            $scope.ownercollection = data.ownercollection;
            $scope.agentcollection = data.agentcollection;
            $scope.agent_can_manage =
              data.pre_settings.agent_can_manage_reminder_automation;
            if ($scope.is_admin_agent_group && $scope.is_admin) {
              $scope.filters.apointment_agent_group_id = -1;
            } else {
              $scope.filters.apointment_agent_group_id = -2;
              if ($scope.agent_can_manage == 2  && !$scope.is_admin) {
                $scope.tabs_shift(2);
              }
            }
            $scope.AgentApointReminderFilter();
          })
          .error(function () {
            $scope.is_loadings = false;
            Loader.hide();
          })
          .finally(function () {
            $scope.is_loading = false;
            Loader.hide();
          });
      };
      $scope.AgentAutoReminderFilter = function () {
        if (!$scope.referrerreminderlist) {
          console.log("Found Empty");
          // Handle the case when the referrerreminderlist is null or undefined
          return;
        }
        var referrer_rem = Array.isArray($scope.referrerreminderlist)
          ? $scope.referrerreminderlist
          : Object.values($scope.referrerreminderlist);
        if ($scope.filters.automation_agent_group_id == -2) {
          // Show all users
          $scope.filteredReferrerReminderList = referrer_rem;
        } else if ($scope.filters.automation_agent_group_id == -1) {
          // Show users with specific agent Group
          $scope.filteredReferrerReminderList = referrer_rem.filter(function (
            referrer
          ) {
            return referrer.admin_user_id == Customer.customer.id;
          });
        } else if ($scope.filters.automation_agent_group_id >= 0) {
          // Show users with specific agent name
          $scope.filteredReferrerReminderList = referrer_rem.filter(function (
            user
          ) {
            return user.sponsor_id == $scope.filters.automation_agent_group_id;
          });
        }
        $scope.is_loading_ref_rem=false;  
      };
      $scope.AgentApointReminderFilter = function () {
        console.log("Filter MEthod Called");
        console.log($scope.filters.apointment_agent_group_id);
        if (!$scope.reminderlist) {
          console.log("Found Empty");
          // Handle the case when the reminderlist is null or undefined
          return;
        }
        var appoint_reminder = Array.isArray($scope.reminderlist)
          ? $scope.reminderlist
          : Object.values($scope.reminderlist);
        if ($scope.filters.apointment_agent_group_id == -2) {
          // Show all users
          $scope.filteredreminderlist = appoint_reminder;
        } else if ($scope.filters.apointment_agent_group_id == -1) {
          // Show users with specific agent Group
          $scope.filteredreminderlist = appoint_reminder.filter(function (
            referrer
          ) {
            return referrer.admin_user_id == Customer.customer.id;
          });
        } else if ($scope.filters.apointment_agent_group_id >= 0) {
          // Show users with specific agent name
          $scope.filteredreminderlist = appoint_reminder.filter(function (
            user
          ) {
            return user.sponsor_id == $scope.filters.apointment_agent_group_id;
          });
        }
      };
      $scope.getreferrerreminders = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getreferrerreminders(Customer.customer.id)
          .success(function (data) {
            $scope.autoStatusFilter("", "allactivestatus");
            $scope.referrerreminderlist = data.remindercollection;
            $scope.filteredReferrerReminderList = data.remindercollection;
            $scope.referrercollection = data.referrercollection;
            $scope.autoagentcollection = data.agentcollection;
            $scope.is_call_script_enabled = data.is_call_script_enabled;
            // Deprecated old agent filter
            // if (data.is_admin) {
            //   $scope.autoagentreminderfilter.agent_name = "adminreminder";
            // } else {
            //   $scope.autoagentreminderfilter.agent_name = "";
            // }
            // Set default filter for agent dropdown
            if ($scope.is_admin_agent_group && $scope.is_admin) {
              $scope.filters.automation_agent_group_id = -1;
            } else {
              $scope.filters.automation_agent_group_id = -2;
            }
            $scope.AgentAutoReminderFilter();                      
          })
          .error(function () {
            $scope.is_loadings = false;
            Loader.hide();
          })
          .finally(function () {
            $scope.is_loading = false;
            Loader.hide();            
          });
      };
      $scope.getfiltericon = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getfiltericon()
          .success(function (data) {
            $scope.filter_icon_done = data.iconCollection.filter_icon_done;
            $scope.filter_icon_all = data.iconCollection.filter_icon_all;
            $scope.filter_icon_cancele =
              data.iconCollection.filter_icon_cancele;
            $scope.filter_icon_pending =
              data.iconCollection.filter_icon_pending;
            $scope.filter_icon_postpone =
              data.iconCollection.filter_icon_postpone;
          })
          .error(function () {
            $scope.is_loadings = false;
            Loader.hide();
          })
          .finally(function () {
            $scope.is_loading = false;
            Loader.hide();
          });
      };     
      $scope.loadAgentGroup = function () {
        Migareference.loadAgentGroup()
          .success(function (data) {
            $scope.agent_group_collection = data.agent_group_collection;
            // Set the default selection to -1 (Admin Agents Group)
            $scope.filters.automation_agent_group_id = -1;
            $scope.filters.apointment_agent_group_id = -1;
          })
          .error(function () {
            $scope.is_loading = false;
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.userType = function () {
        Migareference.userType(Customer.customer.id)
          .success(function (data) {
            $scope.is_admin = data.is_admin;
            $scope.is_admin_agent_group = data.is_admin_agent_group;
            $scope.is_agent = data.is_agent;
          })
          .error(function () {
            $scope.is_loading = false;
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      // Navigate to Report Details Page
      $scope.showdetail = function (report_id) {
        $scope.closeDayBookModal();
        $state.go("report-detailv2", {
          value_id: $stateParams.value_id,
          report_id: report_id,
        });
      };
      // Navigate to Phonebook Page      
      $scope.openPhonebook = function (phobebook_id) {
        $scope.closeDayBookModal();
        $state.go("phonedetail-listv2", {
          value_id: $stateParams.value_id,
          id: phobebook_id,
        });
      };
      // Navigate to Prompt Modal
      $scope.openPromptModal = function (phonebook_id) {        
        $scope.phonebook_id=phonebook_id;      
        $ionicModal.fromTemplateUrl('features/migareference/assets/templates/l1/modal/callscriptprompt.html', {
            scope: $scope,
            animation: 'slide-in-right-left'        
        }).then(function(modal) {
          $scope.callscriptmodal = modal;
          $scope.callscriptmodal.show();          
          $scope.getCallScript(phonebook_id);
        });
      };
      // Navigate to Daybook Modal
      $scope.openDayBookModal = function (daybook_item) {        
        $scope.daybook_item=daybook_item;      
        console.log("daybook_item",daybook_item);
        $ionicModal.fromTemplateUrl('features/migareference/assets/templates/l1/modal/daybook.html', {
            scope: $scope,
            animation: 'slide-in-right-left'        
        }).then(function(modal) {
          $scope.daybookmodal = modal;
          $scope.daybookmodal.show();                    
        });
      };
      $scope.trustAsHtml = function(html) {
        return $sce.trustAsHtml(html);
      };
      // Navigate to Daybook Modal
      $scope.markAsDone = function (entries) {   
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template:
            $translate.instant(
              "Are you sure you want to update this reminder"
            ) + "?",
          cancelText: "No",
          okText: $translate.instant("Yes"),
        });
        confirmPopup.then(function (res) {
          if (res) {
            $scope.is_loading = true;
            Loader.show();
            angular.forEach(entries, function(entry) {
              Migareference.markAsDone(entry.migareference_reminder_daybook_id)
                .success(function(response) {
                  console.log("Marked as done:", entry.migareference_reminder_daybook_id);                  
                })
                .error(function(error) {                  
                  console.error("Error updating:", entry.migareference_reminder_daybook_id, error);
                });
            }); 
            Dialog.alert("Success", "Status successfully updated.", "OK");
            $scope.is_loading = false;
            Loader.hide();
            $scope.loadDaybook(Customer.customer.id);
          }
        });                   
      };
      $scope.getCallScript = function (phonebook_id){
        $scope.isLoading = true;  
        Migareference.getCallScript(phonebook_id)
        .success(function (data) {            
          console.log("Script Data");
          console.log(data);
          $scope.call_script=data.response;
        })
        .error(function (data) {
          $scope.is_loading = false;
          Dialog.alert($translate.instant("Warning"), data.message, "OK");
        })
        .finally(function () {
          $scope.isLoading = false;
        });
      }
      $scope.closeScriptModal = function (){
        $scope.callscriptmodal.remove();
      }
      $scope.closeDayBookModal = function (){
        $scope.daybookmodal.remove();
      }
      $scope.userType();
      $scope.loadAgentGroup();      
      $scope.getreportreminders(1);
      $scope.getreferrerreminders();
      $scope.getfiltericon();
      $scope.getreportremindertype();
    }
  );
