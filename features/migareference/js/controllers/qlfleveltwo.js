angular
  .module("starter")
  .controller("QlfLevelTwoController", function (
    $scope, $stateParams, Migareference, Loader
  ) {
    $scope.migareference = { page_title: "Level Two" };

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
  });
