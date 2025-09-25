angular
  .module("starter")
  .controller(
    "MigareferencephonebooklistController",
    function (
      Loader,
      $scope,
      $state,
      $stateParams,
      Customer,
      $translate,
      $filter,
      Migareference,
      Dialog,
      Modal,
      $ionicPopup
    ) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
        prospectfilter: { ratingfilter: "" },
        referrerfilter: {
          job_title: "",
          profession_title: "",
          province_title: "",
          ratingfilter: "",
          report_lifetime: "",
          days_since_last_contact: "",
          days_range_filter: "",
        },
        migareferencenewphone: {},
        refPhoOrder:'-created_at',
        referrerorderby : {orderkey:'-created_at'},        
        rating: 1,
        grandtotalReferrers:0,
        currentPage:1,
        recordsPerPage:100,
        ratingVal: 0,
        prospectrating: 1,
        gdpr_consent: "",
        totalReferrers:0
      });
      $scope.filters = {
        referrer_agent_group_id: -1,
        prospect_agent_group_id: -1,
      };
      $scope.home_icos=Migareference.app_content;
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
        } else {
          $scope.show_tab1_content = false;
          $scope.show_tab2_content = false;
          $scope.show_tab3_content = true;
          $scope.tab1_css = "inactive_tab";
          $scope.tab2_css = "inactive_tab";
          $scope.tab3_css = "active_tab";
        }
      };
      $scope.phoneDetail = function (id) {
        //phontype:(1 Referrer, 2 Prospect)
        $state.go("phonedetail-listv2", {
          value_id: $stateParams.value_id,
          id: id,
        });
      };
      $scope.addNewPhoneNumber = function (type) {
        $state.go("addphone", {
          value_id: $stateParams.value_id,
          type: type,
        });
      };
      $scope.getphonebooks = function () {
        $scope.is_loading = true;
        Loader.show();
        $scope.getReferrerPhonebook();        
      };
      $scope.ReferrerSortBy = function () {        
        $scope.refPhoOrder=$scope.referrerorderby.orderkey;        
      };
      $scope.getProspectPhonebook = function () {         
        Migareference.getProspectPhonebook(          
          $scope.prospectrating,
          Customer.customer.id
        )
          .success(function (data) {            
            $scope.prospectphonebookcollection = data.prospectPhonebook;
            $scope.filteredProspectUsers = data.prospectPhonebook;
            $scope.contactphonebookcolleaction = data.contactPhonebook;                                                
            // Set default filter for agent dropdown
            if ($scope.is_admin_agent_group && $scope.is_admin) {              
              $scope.filters.prospect_agent_group_id = -1;
            } else {              
              $scope.filters.prospect_agent_group_id = -2;
            }
            // $scope.agentPros();            
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
      $scope.loadReferrerPage = function () {         
        $scope.currentPage++;
        $scope.getReferrerPhonebook();
      };
    
      $scope.getReferrerPhonebook = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getReferrerPhonebook(
          $scope.ratingVal,          
          Customer.customer.id,
          $scope.currentPage,
          $scope.recordsPerPage
        )
          .success(function (data) {
             // Append new data to existing data
            if ($scope.currentPage === 1) {
                $scope.referrersphonebookcollection = data.referrerPhonebook;
            } else {
                $scope.referrersphonebookcollection.push(...data.referrerPhonebook);
            }
            $scope.filteredReferrerUsers = $scope.referrersphonebookcollection;   
            $scope.totalReferrers=$scope.referrersphonebookcollection;         
            $scope.grandtotalReferrers=data.grandtotalReferrers;         
            $scope.jobs_list = data.jobs_list;//Could be moved to a global variable
            $scope.profession_list = data.profession_list;//Could be moved to a global variable
            $scope.province_list = data.province_list;//Could be moved to a global variable
            $scope.engagment_list = data.engagment_list;//Could be moved to a global variable
            $scope.reports_filter_list = data.reports_filter_list;//Could be moved to a global variable
            $scope.ref_filter_lastcontact = data.ref_filter_lastcontact;//Could be moved to a global variable
            $scope.ref_sort_by_filter = data.ref_sort_by_filter;//Could be moved to a global variable
            $scope.referrerorderby.orderkey = '-created_at';
            $scope.ref_filter_date_range = data.ref_filter_date_range;//Could be moved to a global variable            
            $scope.icon_list = data.icon_list;//Could be moved to a global variable
            // Set default filter for agent dropdown
            if ($scope.is_admin_agent_group && $scope.is_admin) {
              $scope.filters.referrer_agent_group_id = -1;              
            } else {
              $scope.filters.referrer_agent_group_id = -2;             
            }            
            $scope.filters.referrer_full_phonebook_id = Customer.customer.id;              
            $scope.applyFilter();
          })
          .error(function () {
            $scope.is_loadings = false;
            Loader.hide();
          })
          .finally(function () {
            if ($scope.currentPage==1) {
              $scope.getProspectPhonebook();
            }
            $scope.is_loading = false;
            Loader.hide();            
          });
      };
      $scope.transferReferrer = function (customer_id) {
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template: $translate.instant(
            "By creating a referrer user manually we cannot collect the approval of Term and Condition. Be sure to collect them offline!"
          ),
          okText: $translate.instant("OK"),
        });
        confirmPopup.then(function (res) {
          if (res) {
            Migareference.transferReferrer(customer_id)
              .success(function (data) {
                Dialog.alert("Success", data.message, "OK");
                $scope.getphonebooks();
                setTimeout(function () {
                  $scope.modal.hide();
                }, 1000);
              })
              .error(function () {
                $scope.is_loadings = false;
                Loader.hide();
              })
              .finally(function () {
                $scope.is_loading = false;
                Loader.hide();
              });
          }
        });
      };
       
      $scope.applyFilter = function() {
        $scope.is_loading = true;
        Loader.show();
        console.log("B-Filter");
        console.log($scope.filteredReferrerUsers);
        console.log($scope.referrersphonebookcollection);
        // Start with the full collection
        $scope.filteredReferrerUsers = Array.isArray($scope.referrersphonebookcollection) 
            ? $scope.referrersphonebookcollection 
            : Object.values($scope.referrersphonebookcollection);
        console.log($scope.filteredReferrerUsers);
        // Filter by job title
        if ($scope.referrerfilter.job_title) {
          console.log("job");
            $scope.filteredReferrerUsers = $filter('filter')($scope.filteredReferrerUsers, {job_title: $scope.referrerfilter.job_title});
        }
        
        // Filter by province title
        if ($scope.referrerfilter.province_title) {
          console.log("province");
            $scope.filteredReferrerUsers = $filter('filter')($scope.filteredReferrerUsers, {province_title: $scope.referrerfilter.province_title});
        }
    
        // Filter by profession title
        if ($scope.referrerfilter.profession_title) {
          console.log("professio");
            $scope.filteredReferrerUsers = $filter('filter')($scope.filteredReferrerUsers, {profession_title: $scope.referrerfilter.profession_title});
        }         
    
        // Filter by report lifetime
        if ($scope.referrerfilter.report_lifetime) {
          console.log("reports");
          if ($scope.referrerfilter.report_lifetime !== "") {
              var min = parseInt($scope.referrerfilter.report_lifetime);
              var max = 1000000; // Default max number of reports         
              switch (min) {
                  case 0:
                      max = 5;
                      break;
                  case 5:
                      max = 10;
                      break;
                  case 10:
                      max = 30;
                      break;
                  case 30:
                      max = 50;
                      break;                 
              }
              $scope.filteredReferrerUsers = $filter('filter')($scope.filteredReferrerUsers, function(referrer) {
                var total_reports = parseInt(referrer.total_reports);
                return total_reports >= min && total_reports <= max;
            });

          }
        }

          // Filter by days since last contact
        if ($scope.referrerfilter.days_since_last_contact !== "") {
          console.log("last contat");
          var lastContactFilter = parseInt($scope.referrerfilter.days_since_last_contact);
          var minDays = 0;
          var maxDays = 1000000; // Default max

          switch (lastContactFilter) {
              case 1:
                  minDays = 0;
                  maxDays = 30;
                  break;
              case 2:
                  minDays = 30;
                  maxDays = 90;
                  break;
              case 3:
                  minDays = 90;
                  maxDays = 1000000;
                  break;
              // Add more cases as needed
          }

          $scope.filteredReferrerUsers = $scope.filteredReferrerUsers.filter(function(referrer) {
              var lastContactDays = parseInt(Math.abs(referrer.lastcontact));
              return lastContactDays >= minDays && lastContactDays <= maxDays;
          });
        }
         // Filter by date range
        if ($scope.referrerfilter.days_range_filter) {
          console.log("daysreange");
          var endDate = new Date(); // Current date
          var startDate = new Date(); // Will be set to the start of the selected range

          switch (parseInt($scope.referrerfilter.days_range_filter)) {
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

          $scope.filteredReferrerUsers = $scope.filteredReferrerUsers.filter(function (referrer) {
              var createdDate = new Date(referrer.created_at);
              return createdDate >= startDate && createdDate <= endDate;
          });
        }

         // Filter by rating
        if ($scope.ratingVal !== undefined && $scope.ratingVal !== null) {
          console.log("rating");
          if ($scope.ratingVal == 0) {
              // If ratingVal is 0, don't apply rating filter
          } else {
              // Apply rating filter based on ratingVal
              $scope.filteredReferrerUsers = $scope.filteredReferrerUsers.filter(function(referrer) {
                  return referrer.rating == $scope.ratingVal; // Adjust 'rating' to your actual property name
              });
          }
        }
        // Filter based on referrer_agent_group_id
        if ($scope.filters.referrer_agent_group_id != null && $scope.is_admin) {
          console.log("referrer_agent_group_id");
         if ($scope.filters.referrer_agent_group_id == -1) {
              $scope.filteredReferrerUsers = $scope.filteredReferrerUsers.filter(function (referrer) {
                  return referrer.admin_user_id == Customer.customer.id;
              });
          } else if ($scope.filters.referrer_agent_group_id == 0) {
            $scope.filteredReferrerUsers = $scope.filteredReferrerUsers.filter(function (user) {
                return user.sponsor_one == 0 && user.sponsor_two == 0;
            });
          }else if ($scope.filters.referrer_agent_group_id >0) {
                $scope.filteredReferrerUsers = $scope.filteredReferrerUsers.filter(function (user) {
                    return user.sponsor_one == $scope.filters.referrer_agent_group_id || 
                          user.sponsor_two == $scope.filters.referrer_agent_group_id;
                });
            }
        }
        // Filter based on  referrer_full_phonebook_id
        if ($scope.filters.referrer_full_phonebook_id && $scope.is_agent) {  
          console.log("fullPhonebook");       
                $scope.filteredReferrerUsers = $scope.filteredReferrerUsers.filter(function (user) {
                    return user.sponsor_one == $scope.filters.referrer_full_phonebook_id || 
                          user.sponsor_two == $scope.filters.referrer_full_phonebook_id;
                });            
        }
        console.log("A-Filter");
        console.log($scope.filteredReferrerUsers);
        Loader.hide();
    };
      
      $scope.saveNewPhone = function () {
        $scope.is_loading = true;
        Migareference.saveNewPhone($scope.migareferencenewphone)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.getphonebooks();
            setTimeout(function () {
              $scope.modal.hide();
            }, 1000);
          })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };

      $scope.resetReferrerFilters = function () {        
        $scope.referrerfilter.report_lifetime = "";
        $scope.referrerfilter.days_since_last_contact = "";
        $scope.referrerfilter.days_range_filter = "";
        $scope.referrerfilter.engagement_level = "";
        $scope.referrerfilter.job_title = "";
        $scope.referrerfilter.profession_title = "";
        $scope.referrerfilter.province_title = "";
        if ($scope.is_admin_agent_group && $scope.is_admin) {
          $scope.filters.referrer_agent_group_id = -1;
        } else {
          $scope.filters.referrer_agent_group_id = -2;
        }
        $scope.filters.referrer_full_phonebook_id = Customer.customer.id;
        $scope.referrer_rating_filter(0);
      };

    
      
      $scope.loadAgentGroup = function () {
        Migareference.loadAgentGroup(Customer.customer.id)
          .success(function (data) {            
            $scope.agent_group_collection = data.agent_group_collection;
            $scope.agent_fullphonebook_collection = data.agent_fullphonebook_collection;
            $scope.is_allowed_full_phonebook=data.is_allowed_full_phonebook;
            // Set the default selection to -1 (Admin Agents Group)
            $scope.filters.referrer_agent_group_id = -1;
          })
          .error(function () {
            // $scope.is_loading = false;
          })
          .finally(function () {
            // $scope.is_loading = false;
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
            // $scope.is_loading = false;
          })
          .finally(function () {
            // $scope.is_loading = false;
          });
      };
      $scope.userType();
      $scope.loadAgentGroup();      
      $scope.getphonebooks();
      // Manula Reating Section
      $scope.referrer_rating_class1 = "inactive-rating";
      $scope.referrer_rating_class2 = "inactive-rating";
      $scope.referrer_rating_class3 = "inactive-rating";
      $scope.referrer_rating_class4 = "inactive-rating";
      $scope.referrer_rating_class5 = "inactive-rating";
      $scope.referrer_rating_class0 = "active-rating-red";
      $scope.referrer_rating_filter = function (ratingindex) {
        $scope.ratingVal = ratingindex;
        if (ratingindex == 0) {
          // $scope.referrerfilter.ratingfilter = "";
          $scope.referrer_rating_class0 = "active-rating-red";
        } else {
          // $scope.referrerfilter.ratingfilter = "rating" + ratingindex;
          $scope.referrer_rating_class0 = "inactive-rating-red";
        }
        if (ratingindex >= 1) {
          $scope.referrer_rating_class1 = "active-rating";
        } else {
          $scope.referrer_rating_class1 = "inactive-rating";
        }
        if (ratingindex >= 2) {
          $scope.referrer_rating_class2 = "active-rating";
        } else {
          $scope.referrer_rating_class2 = "inactive-rating";
        }
        if (ratingindex >= 3) {
          $scope.referrer_rating_class3 = "active-rating";
        } else {
          $scope.referrer_rating_class3 = "inactive-rating";
        }
        if (ratingindex >= 4) {
          $scope.referrer_rating_class4 = "active-rating";
        } else {
          $scope.referrer_rating_class4 = "inactive-rating";
        }
        if (ratingindex >= 5) {
          $scope.referrer_rating_class5 = "active-rating";
        } else {
          $scope.referrer_rating_class5 = "inactive-rating";
        }
        // Now call applyFilter to update the list based on the new rating
        $scope.applyFilter();
      };
      $scope.prospect_rating_class1 = "inactive-rating";
      $scope.prospect_rating_class2 = "inactive-rating";
      $scope.prospect_rating_class3 = "inactive-rating";
      $scope.prospect_rating_class4 = "inactive-rating";
      $scope.prospect_rating_class5 = "inactive-rating";
      $scope.prospect_rating_class0 = "active-rating-red";
      $scope.prospect_ratingstar_filter = function (ratingindex) {
        $scope.prospectrating = ratingindex;
        if (ratingindex == 0) {
          $scope.prospectfilter.ratingfilter = "";
          $scope.prospect_rating_class0 = "active-rating-red";
        } else {
          $scope.prospectfilter.ratingfilter = "rating" + ratingindex;
          $scope.prospect_rating_class0 = "inactive-rating-red";
        }
        if (ratingindex >= 1) {
          $scope.prospect_rating_class1 = "active-rating";
        } else {
          $scope.prospect_rating_class1 = "inactive-rating";
        }
        if (ratingindex >= 2) {
          $scope.prospect_rating_class2 = "active-rating";
        } else {
          $scope.prospect_rating_class2 = "inactive-rating";
        }
        if (ratingindex >= 3) {
          $scope.prospect_rating_class3 = "active-rating";
        } else {
          $scope.prospect_rating_class3 = "inactive-rating";
        }
        if (ratingindex >= 4) {
          $scope.prospect_rating_class4 = "active-rating";
        } else {
          $scope.prospect_rating_class4 = "inactive-rating";
        }
        if (ratingindex >= 5) {
          $scope.prospect_rating_class5 = "active-rating";
        } else {
          $scope.prospect_rating_class5 = "inactive-rating";
        }
      };
    }
  );
