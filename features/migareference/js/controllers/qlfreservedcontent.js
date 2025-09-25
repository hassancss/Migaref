angular
  .module("starter")
  .controller("QlfReservedContentController", function (
    $scope,
    $stateParams,
    Migareference,
    Loader,
    Customer,
    $state
  ) {
    $scope.migareference = { page_title: "Qualification Reserved" };
    $scope.home_icos = null;

    $scope.loadHomecontent = function () {
      $scope.is_loading = true;
      Loader.show();
      Migareference.loadHomecontent()
        .success(function (data) {
          $scope.home_icos = Migareference.app_content = data.app_content;
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

    // From Level One to Level Two
    $scope.goToLevelTwo = function () {
      $state.go("migareference-qlfleveltwo", {
        value_id: $stateParams.value_id,
      });
    };

    // from Level One to Media
    $scope.goToMedia = function () {
      $state.go("migareference-qlfmedia", {
        value_id: $stateParams.value_id,
      });
    };
  });
