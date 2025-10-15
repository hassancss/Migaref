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
    $scope.is_loading = true; 
    $scope.state_name = null;
    $scope.state_params = null;

    $scope.loadHomecontent = function () {
      Loader.show();
      Migareference.loadHomecontent()
        .success(function (data) {
          $scope.home_icos = Migareference.app_content = data.app_content;
        })
        .error(function () {
          console.error("Error loading content");
        })
        .finally(function () {
          $scope.is_loading = false; 
          Loader.hide();
        });
    };

    $scope.loadHomecontent();

    

    $scope.goToLevelTwo = function () {
      $state.go("migareference-qlfleveltwo", {
        value_id: $stateParams.value_id,
      });
    };
   
  });
