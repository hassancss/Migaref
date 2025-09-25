angular
  .module("starter")
  .controller(
    "MigareferenceredeemprizedetailController",
    function (Loader, $scope, $state, $stateParams, Customer, Migareference) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
        app_id: $stateParams.app_id,
        prize_id: $stateParams.prize_id,
        prize_item: {},
        credit_balance: 0,
        home_icos: null,
      });
      $scope.goToHomePage = function () {
        $state.go("home");
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
      $scope.loadRedeemprizeitem = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadRedeemprizeitem(
          Customer.customer.id,
          $stateParams.prize_id
        )
          .success(function (data) {
            $scope.prize_item = data.prize_item;
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
      $scope.totalCredits();
      $scope.loadRedeemprizeitem();
    }
  );
