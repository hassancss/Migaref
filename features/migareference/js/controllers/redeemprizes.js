angular
  .module("starter")
  .controller(
    "MigareferenceredeemprizesController",
    function (
      Loader,
      $scope,
      $session,
      $state,
      $stateParams,
      Customer,
      Migareference,
      $ionicTabsDelegate
    ) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
        getPropertysettings: null,
        owner_warrning: 0,
        type: $stateParams.type,
        listview: true,
        currentFormatBtn: "ion-sb-grid-33",
        currentFormat: "place-100",
        credit_balance: 0,
        home_icos: null,
        collection: [],
      });
      $scope.goToHomePage = function () {
        $state.go("home");
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
      $scope.selectTabWithIndex = function (index) {
        $ionicTabsDelegate.$getByHandle("my-handle").select(2);
      };
      $scope.nextFormat = function (user) {
        switch ($scope.currentFormat) {
          case "place-33":
            $scope.setFormat("place-50", user);
            break;
          case "place-50":
            $scope.setFormat("place-100", user);
            break;
          case "place-100":
          default:
            $scope.setFormat("place-33", user);
            break;
        }
      };
      $scope.setFormat = function (format, user) {
        if (user !== undefined) {
          $session.setItem(
            "migarefrence_redeem_format_" + $stateParams.value_id,
            format
          );
        }
        switch (format) {
          case "place-33":
            $scope.currentFormat = "place-33";
            $scope.currentFormatBtn = "ion-sb-grid-50";
            break;
          case "place-50":
            $scope.currentFormat = "place-50";
            $scope.currentFormatBtn = "ion-sb-list1";
            break;
          case "place-100":
          default:
            $scope.currentFormat = "place-100";
            $scope.currentFormatBtn = "ion-sb-grid-33";
            break;
        }
      };
      $scope.getredeemprizelist = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getredeemprizelist(Customer.customer.id)
          .success(function (data) {
            $scope.collection = data.prizes;
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
      $scope.showItem = function (item) {
        $scope.listview = false;
        $state.go("redeemprize-detailv2", {
          value_id: $scope.value_id,
          app_id: item.app_id,
          prize_id: item.migarefrence_prizes_id,
        });
      };
      $scope.gotoLedger = function () {
        $state.go("prize-ledgerv2", {
          value_id: $stateParams.value_id,
        });
      };
      $scope.gotoshop = function () {
        $state.go("prize-shopv2", {
          value_id: $stateParams.value_id,
        });
      };
      $scope.placeThumbnailSrc = function (item) {
        return item.image_path;
      };
      $scope.totalCredits = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.totalCredits(Customer.customer.id)
          .success(function (data) {
            $scope.credit_balance = data.credit_balance.credits;
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
      $scope.fetchsettings = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.fetchsettings()
          .success(function (payload) {
            $scope.settings = payload.settings;
            $session
              .getItem("migarefrence_redeem_format_" + $stateParams.value_id)
              .then(function (value) {
                if (value) {
                  $scope.setFormat(value);
                } else {
                  $scope.setFormat($scope.settings.default_layout);
                }
              })
              .catch(function () {
                $scope.setFormat($scope.settings.default_layout);
              });
          })
          .error(function () {
            $scope.is_loading = false;
            Loader.hide();
          })
          .finally(function () {
            $scope.is_loading = false;
            Loader.hide();
            $scope.selectTabWithIndex(2);
          });
      };
      $scope.getredeemprizelist();
      $scope.totalCredits();
      $scope.fetchsettings();
      $scope.loadHomecontent();
    }
  );
