angular
  .module("starter")
  .controller(
    "MigareferenceprizedetailController",
    function (
      Loader,
      $scope,
      $state,
      $stateParams,
      Customer,
      $translate,
      Migareference,
      Dialog,
      $ionicPopup
    ) {
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
      $scope.loadPrizeitem = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.loadPrizeitem($stateParams.prize_id)
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
      $scope.redeemprize = function (redeem_credits) {
        redeem_credits = parseInt(redeem_credits);
        $scope.credit_balance = parseInt($scope.credit_balance);
        if (redeem_credits <= $scope.credit_balance) {
          var confirmPopup = $ionicPopup.confirm({
            title: $translate.instant("Confirm"),
            template:
              $translate.instant("Are you sure you want to redeem this prize") +
              "?",
            cancelText: "No",
            okText: $translate.instant("Yes"),
          });
          confirmPopup.then(function (res) {
            if (res) {
              Migareference.redeemprize($scope.prize_id, Customer.customer.id)
                .success(function (data) {
                  Dialog.alert("Success", data.message, "OK");
                  setTimeout(function () {
                    $state.go("prize-shopv2", {
                      value_id: $stateParams.value_id,
                    });
                  }, 3000);
                })
                .error(function (data) {
                  Dialog.alert(
                    $translate.instant("Warning"),
                    data.message,
                    "OK"
                  );
                })
                .finally(function () {
                  $scope.is_loading = false;
                });
            }
          });
        } else {
          Dialog.alert(
            $translate.instant("Warning"),
            $translate.instant("Sorry Insufficient credits for this prize."),
            "OK"
          );
        }
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
      $scope.prizeExternallink = function (link) {
        window.open(link, "_system", "location=yes");
      };
      $scope.loadHomecontent();
      $scope.totalCredits();
      $scope.loadPrizeitem();
    }
  );
