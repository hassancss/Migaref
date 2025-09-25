angular
  .module("starter")
  .controller(
    "MigareferencephonedetaillistController",
    function (
      Loader,
      LinkService,
      $scope,
      $sce,
      $state,
      $stateParams,
      Customer,
      $ionicModal,
      $translate,
      Migareference,
      Dialog,
      $window,
      Modal,
      $ionicPopup,
      $window
    ) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
        addlogitem: { phonebook_id: 0 },
        jobform: { job_title: "" },
        deletelogitem: { log_id: 0 },
        referrer_delete: { phonebook_id: 0, invoice_id: 0, user_id: 0 },
        phondetailform: {
          job_id: 0,
          mobile: "+39",
          birth_day: 0,
          birth_month: 0,
          birth_year: 0,
        },
      });
      $scope.available_matching_data='';
      $scope.available_matching_data_collection={};
      $scope.matched_data='';
      $scope.matched_data_collection={};
      $scope.last_matching_call='';
      $scope.is_first_call_done=true;
      $scope.enable_multi=0;
      $scope.notefield = {
        phonebook_id: 0,
        user_id: Customer.customer.id,
        notes_content: "",
      };
      $scope.ratingVal = 1;
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

      $scope.openModel = function (modelName) {
        console.log("Model Called");
        console.log(modelName);
        Modal.fromTemplateUrl(modelName, {
          scope: $scope,
        }).then(function (modal) {
          if (modelName == "newjobmodel.html") {
            if ($scope.phondetailform.job_id == -1) {
              $scope.modal = modal;
              $scope.modal.show();
            }
          } else if (modelName == "confirmdob.html") {
            //check if DOB is enabled and filled or not
            $scope.dobmodal = modal;
            if (
              ($scope.pre_report_data.mandatory_birthdate == 1 && $scope.pre_report_data.enable_birthdate==1 &&
                $scope.phondetailform.birth_day == 0) ||
              $scope.phondetailform.birth_month == 0 ||
              $scope.phondetailform.birth_year == 0
            ) {
              $scope.dobmodal.show();
            }else{
              $scope.savePhoneDetail();
            }
          } else {
            $scope.modal = modal;
            $scope.modal.show();
          }
        });
      };
      $scope.saveNote = function () {
        $scope.is_loading = true;
        $scope.notefield.phonebook_id = $scope.phonebook_id;
        Migareference.addCoommunicationLog($scope.notefield)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.getphonebookdetail();
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
      // Navigate to Matching Modal
      $scope.referrer_id = 0;
      $scope.openAiMatchingModel = function (referrer_id) {        
        $scope.referrer_id=referrer_id;      
        $ionicModal.fromTemplateUrl('features/migareference/assets/templates/l1/modal/aimatching.html', {
            scope: $scope,
            animation: 'slide-in-right-left'        
        }).then(function(modal) {
          $scope.aimatchingmodal = modal;
          $scope.aimatchingmodal.show();          
          $scope.getAiMatching(referrer_id);
        });
      };
      $scope.closeMatchingModal = function () {                          
          $scope.aimatchingmodal.hide();          
      };
      $scope.getAiMatching = function (referrer_id) {
        $scope.is_loading = true;
        $scope.tabs_shift(1);
        Migareference.getAiMatching(referrer_id)
          .success(function (data) {
            $scope.available_matching_data = data.available_matching_data;
            $scope.available_matching_data_collection = data.available_matching_data_collection;
            $scope.matched_data_collection = data.matched_data_collection;
            $scope.discard_data_collection = data.discard_data_collection;
            $scope.last_matching_call = data.last_matching_call;
            $scope.is_first_call_done = data.is_first_call_done;
            $scope.token_used = data.token_used;            
          })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
            Loader.hide();
          });
      };
      $scope.refreshAiMatching = function (is_first_call) {
        var message = $translate.instant("By refreshing we will discard current referred showed, later you can manage discard in the specific section.")+"?";
        if(is_first_call==1){
          message = $translate.instant("Are you sure you want to proceed")+"?";
        }
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template:message,
          cancelText: "No",
          okText: $translate.instant("Yes"),
        });
        confirmPopup.then(function (res) {
          if (res) {
            Loader.show();
            Migareference.refreshAiMatching($scope.phonebook_id)
              .success(function (data) {
                console.log(data);
              })
              .error(function (data) {
                Dialog.alert($translate.instant("Warning"), data.message, "OK");
              })
              .finally(function () {            
                $scope.getAiMatching($scope.referrer_id);
              });
            }
        });
      };
      $scope.matchCustomer = function (matching_network_id) {        
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template:
            $translate.instant("Are you sure you want to Match customer") +"?",
          cancelText: "No",
          okText: $translate.instant("Yes"),
        });
        confirmPopup.then(function (res) {
          if (res) {
          Loader.show();
            Migareference.matchCustomer(matching_network_id)
              .success(function (data) {
                console.log(data);
              })
              .error(function (data) {
                Dialog.alert($translate.instant("Warning"), data.message, "OK");
              })
              .finally(function () {            
                $scope.getAiMatching($scope.referrer_id);
              });
            }
        });
      };
      $scope.discardCustomer = function (matching_network_id) {        
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template:
            $translate.instant("Are you sure you want to discard") +"?",
          cancelText: "No",
          okText: $translate.instant("Yes"),
        });
        confirmPopup.then(function (res) {
          if (res) {
          Loader.show();
            Migareference.discardCustomer(matching_network_id)
              .success(function (data) {
                console.log(data);
              })
              .error(function (data) {
                Dialog.alert($translate.instant("Warning"), data.message, "OK");
              })
              .finally(function () {            
                $scope.getAiMatching($scope.referrer_id);
              });
            }
        });
      };
      $scope.unMatchCustomer = function (matching_network_id) {
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template:
            $translate.instant("Are you sure you want to proceed with the unmatch") +"?",
          cancelText: "No",
          okText: $translate.instant("Yes"),
        });
        confirmPopup.then(function (res) {
          if (res) {
          Loader.show();
            Migareference.unMatchCustomer(matching_network_id)
              .success(function (data) {
                console.log(data);
              })
              .error(function (data) {
                Dialog.alert($translate.instant("Warning"), data.message, "OK");
              })
              .finally(function () {            
                $scope.getAiMatching($scope.referrer_id);
              });
            }
        });
      };
      $scope.removeCustomer = function (matching_network_id) {
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template:
            $translate.instant("Are you sure you want to Remove from discard list") +"?",
          cancelText: "No",
          okText: $translate.instant("Yes"),
        });
        confirmPopup.then(function (res) {
          if (res) {
          Loader.show();
            Migareference.removeCustomer(matching_network_id)
              .success(function (data) {
                console.log(data);
              })
              .error(function (data) {
                Dialog.alert($translate.instant("Warning"), data.message, "OK");
              })
              .finally(function () {            
                $scope.getAiMatching($scope.referrer_id);
              });
            }
        });
      };
      $scope.openPhonebook = function (phobebook_id) {
        // Hide modal
        $scope.aimatchingmodal.hide();        
        $state.go("phonedetail-listv2", {
          value_id: $stateParams.value_id,
          id: phobebook_id,
        });
      };
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
          $scope.show_tab1_content = false;
          $scope.show_tab2_content = true;
          $scope.show_tab3_content = false;
          $scope.tab1_css = "inactive_tab";
          $scope.tab2_css = "active_tab";
          $scope.tab3_css = "inactive_tab";
        }else{
          $scope.show_tab1_content = false;
          $scope.show_tab2_content = false;
          $scope.show_tab3_content = true;
          $scope.tab1_css = "inactive_tab";
          $scope.tab2_css = "inactive_tab";
          $scope.tab3_css = "active_tab";
        }
      };
      $scope.saveJob = function () {
        $scope.is_loading = true;
        Migareference.addNewJob($scope.jobform)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.getphonebookdetail();
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
      $scope.addhistory = function () {
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Communications Traker"),
          template:
            $translate.instant(
              "Do you want to show the history of communications or add a new record"
            ) + "?",
          cancelText: $translate.instant("LOGS"),
          okText: $translate.instant("ADD"),
        });
        confirmPopup.then(function (res) {
          if (res) {
            $scope.openModel("phonelognote.html");
          } else {
            $scope.openModel("phonelog.html");
          }
        });
      };
      $scope.deleteCommunicationLog = function (logid) {
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
            $scope.deletelogitem.log_id = logid;
            Migareference.deleteCommunicationLog($scope.deletelogitem)
              .success(function (data) {
                $scope.getphonebookdetail();
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
      $scope.deleteReferrer = function (phobebook_id, invoice_id, user_id) {
        console.log("Function is called");
        var confirmPopup = $ionicPopup.confirm({
          title: $translate.instant("Confirm"),
          template: $translate.instant("Are you sure you want to delete") + "?",
          cancelText: "No",
          okText: $translate.instant("Yes"),
        });
        confirmPopup.then(function (res) {
          if (res) {
            $scope.is_loading = true;
            Loader.show();
            $scope.referrer_delete.phonebook_id = phobebook_id;
            $scope.referrer_delete.invoice_id = invoice_id;
            $scope.referrer_delete.user_id = user_id;
            Migareference.deleteReferrer($scope.referrer_delete)
              .success(function (data) {
                Dialog.alert("Success", data.message, "OK");
                setTimeout(function () {
                  $state.go("phonebook-listv2", {
                    value_id: $stateParams.value_id,
                  });
                }, 3000);
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

      $scope.getphonebookdetail = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getphonebookdetail($stateParams.id)
          .success(function (data) {
            $scope.jobs_list = data.jobscollection;
            $scope.profession_list = data.professionscollection;
            $scope.phondetailform = data.phonebookitem;
            $scope.phonebook_id = data.phonebookitem.migarefrence_phonebook_id; //For Prospect entry this will hold migarefrence_prospect_id
            $scope.customer_agent_collection = data.agentcollection;
            $scope.partner_agent_collection = data.partner_agent_collection;
            $scope.consent_collection = data.consent_collection;
            $scope.phonelog = data.phonelog;
            $scope.icon_list = data.icon_list;
            $scope.agnet_province_list = data.agnet_province_list;
            $scope.countries_count = data.countries_count;
            $scope.address_country_list = data.countries_list;
            $scope.address_province_list = data.address_province_list;
            $scope.is_terms_accepted = data.is_terms_accepted;
            $scope.gdpr_consent = $sce.trustAsHtml(data.gdpr_consent);
            $scope.pre_report_data = data.pre_report[0];
            $scope.enable_multi=$scope.pre_report_data.enable_multi_agent_selection;            
            $scope.ratingVal = parseInt($scope.phondetailform.rating);
            $scope.ratingstar_filter($scope.ratingVal);
            $scope.ratingVal = 3;
            $scope.is_referrer = true;
            $scope.show_exclude_fields = false;
            if ($scope.phondetailform.type == 2) {
              $scope.is_referrer = false;
              $scope.show_exclude_fields = true;
            }
            if (data.countries_count < 2) {
              $scope.phondetailform.address_country_id =
                data.default_country_id;
            }
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
      $scope.getprovince = function () {
        $scope.is_loading = true;
        Loader.show();
        address_country_id = $scope.phondetailform.address_country_id;
        Migareference.getprovince(address_country_id)
          .success(function (data) {
            $scope.address_province_list = data.address_province_list;
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
      $scope.getphonebookdetail();
      $scope.savePhoneDetail = function () {
        $scope.is_loading = true;
        $scope.phondetailform.change_by = Customer.customer.id;
        Migareference.savePhoneDetail($scope.phondetailform)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.dobmodal.hide();
            setTimeout(function () {
              $state.go("phonebook-listv2", {
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
      $scope.savePhoneRating = function () {
        $scope.is_loading = true;
        $scope.phondetailform.change_by = Customer.customer.id;
        Migareference.savePhoneDetail($scope.phondetailform)
          .success(function (data) {
            console.log("Rating Saved");
          })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      // Formate PhooneNumber to International Standard
      //#IF the number is not starting with international code "+" or "00" we add by default +39
      $scope.call = function () {
        let callTo = $scope.phondetailform.mobile;
        if (callTo.slice(0, 1) != "+" && callTo.slice(0, 2) != "00") {
          callTo = "+39" + $scope.phondetailform.mobile;
        }        
        LinkService.openLink("tel:" + callTo, {}, true);
      };

      $scope.email = function () {
        $window.open("mailto:" + $scope.phondetailform.email, "_system");
      };
      $scope.whatsapp = function () {
        let callTo = $scope.phondetailform.mobile;
        if (callTo.slice(0, 1) != "+" && callTo.slice(0, 2) != "00") {
          callTo = "+39" + $scope.phondetailform.mobile;
        }
        urltrigger = "https://api.whatsapp.com/send?phone=" + callTo;
        window.open(urltrigger, "_system", "location=yes");
      };
      $scope.rating_class1 = "active-rating";
      $scope.rating_class2 = "inactive-rating";
      $scope.rating_class3 = "inactive-rating";
      $scope.rating_class4 = "inactive-rating";
      $scope.rating_class5 = "inactive-rating";
      $scope.ratingstar_filter = function (ratingindex) {
        $scope.phondetailform.rating = ratingindex;
        if (ratingindex >= 1) {
          $scope.rating_class1 = "active-rating";
        } else {
          $scope.rating_class1 = "inactive-rating";
        }
        if (ratingindex >= 2) {
          $scope.rating_class2 = "active-rating";
        } else {
          $scope.rating_class2 = "inactive-rating";
        }
        if (ratingindex >= 3) {
          $scope.rating_class3 = "active-rating";
        } else {
          $scope.rating_class3 = "inactive-rating";
        }
        if (ratingindex >= 4) {
          $scope.rating_class4 = "active-rating";
        } else {
          $scope.rating_class4 = "inactive-rating";
        }
        if (ratingindex >= 5) {
          $scope.rating_class5 = "active-rating";
        } else {
          $scope.rating_class5 = "inactive-rating";
        }
        $scope.savePhoneRating();
      };

      //Loade Home Contatn
      $scope.loadHomecontent = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadHomecontent()
          .success(function (data) {
            $scope.home_icos = data.app_content;
            $scope.app_content = data.app_content;
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
    }
  );
