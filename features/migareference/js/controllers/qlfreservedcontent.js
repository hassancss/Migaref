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

    //referrerFeatuerContent This method will get dynamic state and parmas goToFeature method
    $scope.referrerFeatuerContent = function () {
      Loader.show();
      Migareference.referrerFeatuerContent(Customer.customer.id)
        .success(function (data) {
          $scope.state_name = data.data[0].state;
          $scope.state_params = data.data[0].params;
          //from array to object
          if (data.state_params) {
            var params = {};
            for (var i = 0; i < data.state_params.length; i++) {
              params[data.state_params[i].key] = data.state_params[i].value;
            }
            $scope.state_params = params;
          }
          console.log($scope.state_name, $scope.state_params);
          
        })
        .error(function () {
          console.error("Error loading content");
        })
        .finally(function () {
          $scope.is_loading = false; 
          Loader.hide();
        });
    };

    $scope.referrerFeatuerContent();

    $scope.goToLevelTwo = function () {
      $state.go("migareference-qlfleveltwo", {
        value_id: $stateParams.value_id,
      });
    };
    $scope.goToFeature = function () {
      console.log($scope.state_name, $scope.state_params);
      
      $state.go($scope.state_name, $scope.state_params);
    };
  });
