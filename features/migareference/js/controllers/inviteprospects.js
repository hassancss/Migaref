angular
  .module("starter")
  .controller(
    "MigareferenceinvitereportsController",
    function (
      Loader,
      $scope,
      $state,
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
        owner_warrning: 0,
        pre_settings: null,
        disablereportSubmit: false,
        home_icos: null,
        property_detail_header: false,
        origin: {
          address: null,
          latitude: null,
          longitude: null,
        },
      });
      $scope.active_class_1 = "inactive_pile";
      $scope.active_class_2 = "inactive_pile";
      $scope.active_class_3 = "active_pile";
      $scope.active_class_4 = "inactive_pile";
      $scope.active_class_5 = "inactive_pile";
      $scope.page_title = "Add New Report";
      $scope.migareferenceformchange = {
        user_id: Customer.customer.id,
        address: "",
        longitude: "",
        latitude: "",
        owner_hot: 3,
      };
      $scope.changeItinerary = function () {
        $scope.migareferenceformchange.latitude = $scope.origin.latitude;
        $scope.migareferenceformchange.longitude = $scope.origin.longitude;
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
      // GO to Home Page
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
            "Confirm",
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
      $scope.prereportsettigns = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.prereportsettigns()
          .success(function (data) {
            $scope.pre_settings = data.pre_settings;
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
      $scope.prereportsettigns();
    }
  );
