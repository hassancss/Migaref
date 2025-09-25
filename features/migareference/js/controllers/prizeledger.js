angular
  .module("starter")
  .controller(
    "MigareferenceprizeledgerController",
    function (
      Loader,
      $scope,
      $state,
      $stateParams,
      Customer,
      Migareference,
      $ionicTabsDelegate
    ) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
        prize_item: {},
        credit_balance: 0,
        collection: [],
        page_title: "Ledger",
        home_icos: null,
        listview: false,
      });
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
      $scope.goToHomePage = function () {
        $state.go("home");
      };
      $scope.selectTabWithIndex = function (index) {
        $ionicTabsDelegate.$getByHandle("my-handle-ledger").select(1);
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
      $scope.loadLedger = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadLedger(Customer.customer.id, $stateParams.prize_id)
          .success(function (data) {
            $scope.collection = data.ledgerdata;
          })
          .error(function () {
            $scope.is_loading = false;
            Loader.hide();
          })
          .finally(function () {
            $scope.is_loading = false;
            Loader.hide();
            $scope.selectTabWithIndex(1);
          });
      };
      $scope.gotoredeemprizes = function () {
        $state.go("redeem-prizesv2", {
          value_id: $stateParams.value_id,
        });
      };
      $scope.gotoshop = function () {
        $state.go("prize-shopv2", {
          value_id: $stateParams.value_id,
        });
      };
      $scope.totalCredits();
      $scope.loadLedger();
      $scope.loadHomecontent();
    }
  );
