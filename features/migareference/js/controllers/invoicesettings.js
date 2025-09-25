angular
  .module("starter")
  .controller(
    "MigareferencesettingsController",
    function (
      Loader,
      $scope,
      $state,
      $sce,
      $stateParams,
      Customer,
      $translate,
      Migareference,
      Dialog,
      Modal
    ) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
        getPropertysettings: null,
        user_id: Customer.customer.id,
        is_created: false,
        is_created_at: "Not Yet",
        agent_list: null,
        is_agent_list: false,
        is_geo_list: false,
        home_icos: null,
      });
      $scope.jobitems = [];
      $scope.professionitems = [];
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
      $scope.itemList = [];
      $scope.agentList = [];
      $scope.blisterPackTemplates = [
        { id: 1, name: "Email/PUSH" },
        { id: 2, name: "Only Email" },
        { id: 3, name: "Only PUSH" },
      ];
      $scope.blisterPackTemplateSelected = $scope.blisterPackTemplates[0];
      $scope.changedValue = function (item) {
        $scope.migareferenceformchange.notification_type = item.id;
      };
      var length = 10,
        charset =
          "abcdefghijklmn45o54pqrst654@@##$6uvwxyzA6574BCDEF54GHIJKLMNOPQRSTUV^&*()WXYZ0123456789",
        blk_password = "";
      for (var i = 0, n = charset.length; i < length; ++i) {
        blk_password += charset.charAt(Math.floor(Math.random() * n));
      }
      $scope.migareferenceformchange = {
        operation: "save",
        user_id: Customer.customer.id,
        invoice_mobile: Customer.customer.mobile,
        invoice_name: Customer.customer.firstname,
        invoice_surname: Customer.customer.lastname,
        blockchain_password: blk_password,
        terms_accepted: false,
        privacy_accepted: false,
        special_terms_accepted: false,
        add_job: true,
        app_short_version: Migareference.app_short_version,
        birth_day: 0,
        birth_month: 0,
        birth_year: 0,
      };
      $scope.app_full_version = Migareference.app_full_version;
      $scope.app_short_version =
        $scope.migareferenceformchange.app_short_version;
      $scope.is_special_terms = false;
      // GO to Home Page
      $scope.goToHomePage = function () {
        $state.go("home");
      };
      $scope.openTermmodel = function () {
        Modal.fromTemplateUrl("showTerms.html", {
          scope: $scope,
        }).then(function (modal) {
          $scope.modal = modal;
          $scope.modal.show();
        });
      };
      $scope.showTerms = function (type) {
        switch (type) {
          case 1:
            $scope.termsTitle = "Privacy";
            $scope.termsDescription = $scope.privacy;
            if ($scope.migareferenceformchange.privacy_accepted) {
              $scope.openTermmodel();
            }
            break;
          case 2:
            $scope.termsTitle = "Terms";
            $scope.termsDescription = $scope.terms;
            if ($scope.migareferenceformchange.terms_accepted) {
              $scope.openTermmodel();
            }
            break;
          case 3:
            $scope.termsTitle = $scope.term_label_text;
            $scope.termsDescription = $scope.special_terms;
            if ($scope.migareferenceformchange.special_terms_accepted) {
              $scope.openTermmodel();
            }
            break;
        }
      };

      $scope.getPropertysettings = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getPropertysettings($scope.user_id)
          .success(function (data) {
            // Sponsor List
            $scope.migareferenceformchange.birth_day =
              data.getPropertysettings[0].birth_day;
            $scope.migareferenceformchange.birth_month =
              data.getPropertysettings[0].birth_month;
            $scope.migareferenceformchange.birth_year =
              data.getPropertysettings[0].birth_year;
            $scope.is_agent_list = data.is_agent_list;
            $scope.agentList = data.agent_list;
            $scope.partneragentList = data.partner_agent_list;
            $scope.migareferenceformchange.sponsor_id = $scope.agentList[0];
            $scope.agnet_province_list = data.agnet_province_list;
            $scope.countries_count = data.countries_count;
            if (data.countries_count < 2) {
              $scope.migareferenceformchange.address_country_id =
                data.default_country_id;
            }
            $scope.address_country_list = data.countries_list;
            $scope.address_province_list = data.address_province_list;
            $scope.jobitems = data.job_list;
            $scope.migareferenceformchange.job_id = $scope.jobitems[0];
            $scope.professionitems = data.profession_list;
            $scope.migareferenceformchange.profession_id = $scope.professionitems[0];
            $scope.is_geo_list = data.is_geo_list;
            if (
              data.getPropertysettings[0].migareference_invoice_settings_id !==
              undefined
            ) {
              if (data.getPropertysettings[0]["privacy_accepted"] == 1) {
                data.getPropertysettings[0]["privacy_accepted"] = true;
              } else {
                data.getPropertysettings[0]["privacy_accepted"] = false;
              }
              if (data.getPropertysettings[0]["terms_accepted"] == 1) {
                data.getPropertysettings[0]["terms_accepted"] = true;
              } else {
                data.getPropertysettings[0]["terms_accepted"] = false;
              }
              if (data.getPropertysettings[0]["special_terms_accepted"] == 1) {
                data.getPropertysettings[0]["special_terms_accepted"] = true;
              } else {
                data.getPropertysettings[0]["special_terms_accepted"] = false;
              }
              $scope.migareferenceformchange = data.getPropertysettings[0];
              $scope.migareferenceformchange.app_short_version =
                $scope.app_short_version;
              $scope.migareferenceformchange.add_job = true;
              $scope.migareferenceformchange.operation = "update";
              $scope.migareferenceformchange.job_id =
                $scope.jobitems[data.getPropertysettings[0]["job_id"]];
              $scope.migareferenceformchange.profession_id =
                $scope.professionitems[data.getPropertysettings[0]["profession_id"]];
              $scope.migareferenceformchange.sponsor_id =
                $scope.agentList[data.getPropertysettings[0]["sponsor_id"]];
            } else {
              $scope.migareferenceformchange.notification_type = 1;
            }
            if (data.getPropertysettings[0].created_at != "") {
              $scope.is_created = true;
              $scope.is_created_at = data.getPropertysettings[0].created_at;
            }
            if ($scope.migareferenceformchange.notification_type == 2) {
              $scope.blisterPackTemplateSelected =
                $scope.blisterPackTemplates[1];
              $scope.migareferenceformchange.notification_type = 2;
            } else if ($scope.migareferenceformchange.notification_type == 3) {
              $scope.blisterPackTemplateSelected =
                $scope.blisterPackTemplates[2];
              $scope.migareferenceformchange.notification_type = 3;
            } else {
              $scope.blisterPackTemplateSelected =
                $scope.blisterPackTemplates[0];
              $scope.migareferenceformchange.notification_type = 1;
            }
            $scope.terms = $sce.trustAsHtml(data.pre_report[0].terms);
            $scope.privacy = $sce.trustAsHtml(data.pre_report[0].privacy);
            if ($scope.terms == "") {
              $scope.terms =
                "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
            }
            if ($scope.privacy == "") {
              $scope.privacy =
                "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
            }
            if (
              data.pre_report[0].term_label_text != "" &&
              data.pre_report[0].special_terms != ""
            ) {
              $scope.is_special_terms = true;
              $scope.term_label_text = data.pre_report[0].term_label_text;
              $scope.special_terms = $sce.trustAsHtml(
                data.pre_report[0].special_terms
              );
            }
            $scope.sponsor_id = data.pre_report[0].sponsor_id;
            $scope.pre_report_data = data.pre_report[0];
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
      $scope.savePropertysettings = function () {
        $scope.is_loading = true;
        $scope.migareferenceformchange.chnage_by = Customer.customer.id;
        Migareference.savePropertysettings($scope.migareferenceformchange)
          .success(function (data) {
            Dialog.alert("Success", data.message, "OK");
            $scope.migareferenceformchange = { operation: "save" };
            setTimeout(function () {
              $scope.goToMainMenu();
            }, 3000);
          })
          .error(function (data) {
            Dialog.alert($translate.instant("Warning"), data.message, "OK");
          })
          .finally(function () {
            $scope.is_loading = false;
          });
      };
      $scope.goToMainMenu = function () {
        $state.go("migareference-view", {
          value_id: $stateParams.value_id,
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
      $scope.getprovince = function () {
        $scope.is_loading = true;
        Loader.show();
        address_country_id = $scope.migareferenceformchange.address_country_id;
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
      $scope.getPropertysettings();
      $scope.loadHomecontent();
    }
  );
