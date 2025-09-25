angular
  .module("starter")
  .controller(
    "MigareferenceshowreportsController",
    function (
      Loader,
      $scope,
      $rootScope,
      $state,
      $stateParams,
      Customer,
      $translate,
      $ionicModal,      
      Migareference,
      Dialog,
      Modal,
      LinkService
    ) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
        getPropertysettings: null,
        owner_warrning: 0,
        home_icos: null,
      });      
      $scope.shareurlmodel = { url: "" };
      $scope.filters = {
        status_id: "",
        from_date: "",
        to_date: "",
        search: "",
        referrer_id:"",
        days_range_filter:"",
        user_id: Customer.customer.id,
        agent_group_id: "",
      };      
      $scope.reportsorderby = {orderkey:'-report_modified_at_filter'};
      $scope.reportPhoOrder='-report_modified_at_filter';
      $scope.pre_settings={};
      defaultReportsCollection={};
      $scope.is_first_call=true;
      $scope.more_filter_btn = true;
      $scope.is_admin = false;
      $scope.is_agent = false;
      $scope.showmore = false;
      $scope.reports = null;
      $scope.stasuses = {};//::
      $scope.can_manage = false;
      $scope.manualfields = { report_id: 0 };
      $scope.page_title = "Property Report Status";
      $scope.consent_invit = "";
      $scope.saveManualConsent = function () {
        $scope.is_loading = true;
        Migareference.saveManualConsent($scope.manualfields)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.loadactivereports();
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
      $scope.currunt_year = new Date().getFullYear();
      $scope.max_year = $scope.currunt_year + 12;
      $scope.years = [
        {
          id: "",
          name: $translate.instant("Year"),
        },
      ];
      for (y = $scope.currunt_year; y <= $scope.max_year; y++) {
        $scope.years.push({ id: y, name: y });
      }
      // GO to Home Page
      $scope.goToHomePage = function () {
        $state.go("home");
      };
      $scope.clearFilter = function () {        
        $scope.filters = {          
          from_date: "",
          to_date: "",
          search: "",
          referrer_id:"",
          days_range_filter:"",
          user_id: Customer.customer.id
        }; 
        if ($scope.is_admin_agent_group && $scope.is_admin) {              
          $scope.filters.agent_group_id = -1;
        } else {              
          $scope.filters.agent_group_id = -2;
        }

        $scope.showmore = false;
        // Set default filter of status
        if ($scope.is_admin || $scope.is_agent) {
          $scope.filters.status_id='-2' //For Admin & Agent 
        }else{
          $scope.filters.status_id='-1'//For Referrer
        }
        $scope.reportsCollection=$scope.defaultReportsCollection;
        $scope.applyFilter();        
      };

      $scope.copyUrl = function () {
        var url = $scope.shareurlmodel.url;
        var copyElement = document.createElement("textarea");
        copyElement.style.position = "fixed";
        copyElement.style.opacity = "0";
        copyElement.textContent = decodeURI(url);
        var body = document.getElementsByTagName("body")[0];
        body.appendChild(copyElement);
        copyElement.select();
        document.execCommand("copy");
        body.removeChild(copyElement);
        $scope.modal.hide();
      };
    
      $scope.showExternalLink = function (report_id) {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getexternallinks(report_id)
          .success(function (data) {
            $scope.user_list = data.user_list;
            Modal.fromTemplateUrl("externalsharelink.html", {
              scope: $scope,
            }).then(function (modal) {
              $scope.modal = modal;
              $scope.modal.show();
            });
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
      $scope.manualConsent = function (item) {
        $scope.consent_invit = item.consent_invit_msg_body;
        $scope.manualfields.report_id = item.report_id;
        if (item.consent_invit_msg_body != "") {
          Modal.fromTemplateUrl("manualconsent.html", {
            scope: $scope,
          }).then(function (modal) {
            $scope.modal = modal;
            $scope.modal.show();
          });
        }
      };
      $scope.shareConsent = function () {
        if ($scope.consent_invit == "") {
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
        $scope.modal.hide();
        return window.plugins.socialsharing.share($scope.consent_invit);
      };
      $scope.applyFilter = function() {
        console.log("Apply Filter");
        console.log($scope.filters);
        console.log($scope.filteredReports);
        console.log($scope.filteredReports.length);        
        Loader.show();
        // Start with the full collection
        $scope.filteredReports = Array.isArray($scope.reportsCollection) 
            ? $scope.reportsCollection 
            : Object.values($scope.reportsCollection);        
         // Filter by date range
        if ($scope.filters.days_range_filter) {
          if (parseInt($scope.filters.days_range_filter)==1000) {//show custom date range
            $scope.showmore = true;
              if ($scope.filters.from_date || $scope.filters.to_date) {
                // Custom date range filter
                var fromDate = $scope.filters.from_date ? new Date($scope.filters.from_date) : null;
                var toDate = $scope.filters.to_date ? new Date($scope.filters.to_date) : null;
                
                console.log("Custom date range");
                console.log(fromDate);
                console.log(toDate);
                
                $scope.filteredReports = $scope.filteredReports.filter(function(report) {
                    // Parse createdDate only if it's a valid date string
                    var createdDate = moment(report.created_at, 'DD-MM-YYYY').toDate();
                    
                    // Check if fromDate and toDate are valid dates
                    var isValidFromDate = fromDate instanceof Date && !isNaN(fromDate);
                    var isValidToDate = toDate instanceof Date && !isNaN(toDate);
                    
                    // Apply filtering based on fromDate and toDate conditions
                    if (isValidFromDate && isValidToDate) {
                        // Filter reports within the date range if both fromDate and toDate are valid
                        return createdDate >= fromDate && createdDate <= toDate;
                    } else if (isValidFromDate) {
                        // Filter reports after fromDate if only fromDate is valid
                        return createdDate >= fromDate;
                    } else if (isValidToDate) {
                        // Filter reports before toDate if only toDate is valid
                        return createdDate <= toDate;
                    } else {
                        // If neither fromDate nor toDate is valid, include all reports
                        return true;
                    }
                });
            }          
          }else{
            $scope.showmore = false;
            var endDate = new Date(); // Current date
            var startDate = new Date(); // Will be set to the start of the selected range
  
            switch (parseInt($scope.filters.days_range_filter)) {
                case 1: // Past 7 Days
                    startDate.setDate(endDate.getDate() - 7);
                    break;
                case 2: // Past 30 Days
                    startDate.setDate(endDate.getDate() - 30);
                    break;
                case 3: // Past 3 Months
                    startDate.setMonth(endDate.getMonth() - 3);
                    break;
                case 4: // Past 6 Months
                    startDate.setMonth(endDate.getMonth() - 6);
                    break;
                case 5: // Past 12 Months
                    startDate.setMonth(endDate.getMonth() - 12);
                    break;
            }
  
            $scope.filteredReports = $scope.filteredReports.filter(function (report) {
              var createdDate = moment(report.created_at, 'DD-MM-YYYY').toDate();
                console.log(createdDate+"@"+report.created_at);
                // console.log("Create at"+startDate+"@"+createdDate+"@"+endDate);
                return createdDate >= startDate && createdDate <= endDate;
            });
          }
        }
        // Filter based on referrer_id
        if ($scope.filters.referrer_id != null && $scope.filters.referrer_id !== undefined && $scope.filters.referrer_id>0) {        
                $scope.filteredReports = $scope.filteredReports.filter(function (report) {
                    return report.referrer_id == $scope.filters.referrer_id;
                });            
        }
        // Filter based on agent_group_id
        if ($scope.filters.agent_group_id != null) {
         if ($scope.filters.agent_group_id == -1) {//Pending is_admin_agent_group define Admin have some connected agents
              // $scope.filteredReports = $scope.filteredReports.filter(function (referrer) {
              //     return referrer.admin_user_id == Customer.customer.id;
              // });
              $scope.filteredReports = $scope.filteredReports.filter(function(report) {
                // Check if any element in $scope.admin_agent matches the condition
                return $scope.admin_agent.some(function(agent) {
                    return agent.user_id == report.sponsor_one || agent.user_id == report.sponsor_two;
                });
            });
          } else if ($scope.filters.agent_group_id == 0) {// Show reports whos referrer is not connected to any agent or Idont know case
            $scope.filteredReports = $scope.filteredReports.filter(function (report) {
                return report.sponsor_one == 0 && report.sponsor_two == 0;
            });
          }else if ($scope.filters.agent_group_id >0) { //Show matched agent either type one agent or type two agent
                $scope.filteredReports = $scope.filteredReports.filter(function (report) {
                    return report.sponsor_one == $scope.filters.agent_group_id || 
                          report.sponsor_two == $scope.filters.agent_group_id;
                });
            }
        }
        console.log("End Filter");
        console.log($scope.filteredReports);
        console.log($scope.filteredReports.length);     
        Loader.hide();
    };
    $scope.ReportSortBy = function () {        
      $scope.reportPhoOrder=$scope.reportsorderby.orderkey;        
    };
      // Loade default contetn for this page
      $scope.loadactivereports = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadactivereports($scope.filters)
          .success(function (data) {
            $scope.stasuses = data.status;
            $scope.enable_gdpr = data.enable_gdpr;
            $scope.reports = data;
            $scope.filteredReports = data.all_reports;
            $scope.reportsCollection = data.all_reports;
            if ($scope.is_first_call) { //This will help me to avoid re-load content on ClearFilter as i have already a copy of default filter
              $scope.defaultReportsCollection = data.all_reports;
              $scope.is_first_call=false;
            }
            $scope.reports_sort_by_filter = data.reports_sort_by_filter;
            $scope.reports_filter_date_range = data.reports_filter_date_range;
            $scope.reportPhoOrder='-report_modified_at_filter';
            if ($scope.reports.is_admin) {
              $scope.can_manage = true;
            } else if (
              $scope.reports.is_agent == 1 &&
              $scope.reports.agent_can_manage == 1
            ) {
              $scope.can_manage = true;
            }
          })
          .error(function () {
            $scope.is_loading = false;
            Loader.hide();
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {            
            $scope.is_loading = false;
            Loader.hide();
          });
      };
      // Go to detail page
      $scope.showdetail = function (report_id) {
        $state.go("report-detailv2", {
          value_id: $stateParams.value_id,
          report_id: report_id,
        });
      };
      // download certificate
      $scope.downloadCertificate = function (report_id) {
        LinkService.openLink(
          BASE_URL +
            "/migareference/public_pdf/download-pdf/report_id/" +
            report_id,
          {},
          true
        );
      };
      // Default Loader
      $scope.loadHomecontent = function () {
        Migareference.loadHomecontent()
          .success(function (data) {
            $scope.home_icos = data.app_content;
          })
          .error(function () {
            $scope.is_loading = false;
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.loadAgentGroup = function () {
        Migareference.loadAgentGroup()
          .success(function (data) {
            $scope.agent_group_collection = data.agent_group_collection;
            $scope.referrer_collection = data.referrer_collection;            
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
            $scope.is_agent = data.is_agent;
            $scope.is_admin_agent_group = data.is_admin_agent_group;
            $scope.admin_agent = data.admin_agent;
            if ($scope.is_admin_agent_group && $scope.is_admin) {              
              $scope.filters.agent_group_id = -1;
            } else {              
              $scope.filters.agent_group_id = -2;
            }
            // Set default filter of status
            if ($scope.is_admin || $scope.is_agent) {
              $scope.filters.status_id='-2' //For Admin & Agent 
            }else{
              $scope.filters.status_id='-1'//For Referrer
            }
            $scope.loadactivereports();
          })
          .error(function () {
            $scope.is_loading = false;
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.prereportsettigns = function () {
        Migareference.prereportsettigns()
          .success(function (data) {
            $scope.pre_settings = data.pre_settings;
          })
          .error(function () {
            $scope.is_loading = false;
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      }; 

      $scope.showNotesList = function(report) {        
        $scope.isLoading = true;        
        $ionicModal.fromTemplateUrl('features/migareference/assets/templates/l1/modal/noteslist.html', {
            scope: $scope,
            animation: 'slide-in-right-left'        
        }).then(function(modal) {
          $scope.modalNotesList = modal;
          $scope.modalNotesList.show();
          report.note_unread_count=0;
          Migareference.showNotesList(report.migareference_report_id)
          .success(function (data) {            
            $scope.notescollection = data.notes;
          })
          .error(function (data) {
            $scope.is_loading = false;
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.isLoading = false;
          });
        });
    };

    $scope.closeModalNotesList = function (){
      $scope.modalNotesList.remove();
    }
      $scope.prereportsettigns();
      $scope.loadAgentGroup();
      $scope.userType();
      $scope.loadHomecontent();
    }
  );
