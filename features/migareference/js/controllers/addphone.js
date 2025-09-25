angular
  .module("starter")
  .controller(
    "MigareferenceaddphoneController",
    function (
      Loader,
      $scope,
      $state,
      $stateParams,
      $translate,
      Migareference,
      Customer,
      Modal,
      Dialog
    ) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
        show_exclude_fields: true,
        jobform: { job_title: "" },
        phondetailform: {
          job_id: 0,
          mobile: "+39",
          email: "",
          type: 1,
          is_exclude: 2,
          is_blacklist: 1,
          birth_day: 0,
          birth_month: 0,
          birth_year: 0,
          type: 0,
        },
      });
      $scope.phondetailform.type = $stateParams.type;
      $scope.enable_multi=0;
      $scope.phone_type = $stateParams.type;
      if ($stateParams.type == 1) {
        Dialog.alert(
          $translate.instant("Confirm"),
          $translate.instant(
            "By creating a referrer user manually we cannot collect the approval of Term and Condition. Be sure to collect them offline!"
          ),
          "OK"
        );
      }
      $scope.jobitems = [];
      $scope.binaryOption = [
        {
          id: 1,
          name: $translate.instant("No"),
        },
        {
          id: 2,
          name: $translate.instant("Yes"),
        },
      ];
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
      $scope.is_blacklist = $scope.binaryOption[0];
      $scope.is_exclude = $scope.binaryOption[0];

      $scope.changeExcludeItem = function (item) {
        $scope.phondetailform.is_exclude = item.id;
      };
      $scope.changeBlacklistItem = function (item) {
        $scope.phondetailform.is_blacklist = item.id;
      };
      $scope.saveJob = function () {
        $scope.is_loading = true;
        Migareference.addNewJob($scope.jobform)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.loadJobslist();
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
      $scope.openModel = function (modelName) {
        Modal.fromTemplateUrl(modelName, {
          scope: $scope,
        }).then(function (modal) {
          if (modelName == "newjobmodel.html") {
            if ($scope.phondetailform.job_id.job_id == -1) {
              $scope.modal = modal;
              $scope.modal.show();
            }
          } else if (modelName == "confirmdob.html") {
            //check if DOB is enabled and filled or not
            if (
                $scope.pre_report_data.mandatory_birthdate == 1 && $scope.pre_report_data.enable_birthdate==1 && ( 
                $scope.phondetailform.birth_day == 0 ||
                $scope.phondetailform.birth_month == 0 ||
                $scope.phondetailform.birth_year == 0)
            ) {
              $scope.modal = modal;
              $scope.modal.show();
            }else{
              $scope.savePhoneDetail(0);
            }
          } else {
            $scope.modal = modal;
            $scope.modal.show();
          }
        });
      };
      $scope.savePhoneDetail = function (is_modal) {        
        if (is_modal) {
          $scope.modal.hide(); 
        }        
        $scope.is_loading = true;
        $scope.phondetailform.change_by = Customer.customer.id;
        Migareference.savePhoneEntry($scope.phondetailform)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
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

      $scope.ratingVal = 1;
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
      $scope.loadJobslist = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadJobslist()
          .success(function (data) {
            $scope.jobitems = data.jobscollection;
            $scope.professionitems = data.professionscollection;
            $scope.pre_report_data = data.pre_report[0];
            $scope.customer_agent_collection = data.agentcollection;
            $scope.partner_agent_collection = data.partner_agent_collection;
            $scope.phondetailform.job_id = $scope.jobitems[0];
            // $scope.phondetailform.job_id = $scope.jobitems[0];
            $scope.countries_count = data.countries_count;
            if (data.countries_count < 2) {
              $scope.phondetailform.address_country_id =
                data.default_country_id;
            }
            $scope.address_country_list = data.countries_list;
            $scope.address_province_list = data.address_province_list;
            $scope.enable_multi=$scope.pre_report_data.enable_multi_agent_selection;            
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

      $scope.loadJobslist();
    }
  );
