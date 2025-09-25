angular
  .module("starter")
  .controller(
    "MigareferencegeneralstatsController",
    function (
      Loader,
      $sce,
      $scope,
      $state,
      $window,
      $stateParams,
      Migareference,
      LinkService
    ) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
      });
      // Objects and defaults
      // $scope.general_stat_filter = { custom_range: 1 };
      // START: Header Tabs
      $scope.tab1_css = "active_tab";
      $scope.tab2_css = "inactive_tab";
      $scope.tab3_css = "inactive_tab";
      $scope.show_tab1_content = true;
      $scope.show_tab2_content = false;
      $scope.show_tab3_content = false;
      $scope.tabs_shift = function (tab_key) {
        if (tab_key == 1) {
          $scope.show_tab1_content = true;
          $scope.show_tab2_content = false;
          $scope.show_tab3_content = false;
          $scope.tab1_css = "active_tab";
          $scope.tab2_css = "inactive_tab";
          $scope.tab3_css = "inactive_tab";
        } else if (tab_key == 2) {
          $scope.show_tab1_content = false;
          $scope.show_tab2_content = true;
          $scope.show_tab3_content = false;
          $scope.tab1_css = "inactive_tab";
          $scope.tab2_css = "active_tab";
          $scope.tab3_css = "inactive_tab";
        } else {
          $scope.show_tab1_content = false;
          $scope.show_tab2_content = false;
          $scope.show_tab3_content = true;
          $scope.tab1_css = "inactive_tab";
          $scope.tab2_css = "inactive_tab";
          $scope.tab3_css = "active_tab";
        }
      };
      // END Header Tabs
      //@*******************************GENERAL*********************************@/
      // Stat Group Widget controls
      $scope.changeBorder = function (widget) {
        console.log("Method widget called");
        // Remove 'active' class from all widgets
        var widgets = document.getElementsByClassName("widget");
        for (var i = 0; i < widgets.length; i++) {
          widgets[i].classList.remove("active");
        }
        // Add 'active' class to the clicked widget
        widget.classList.add("active");
      };
      // Stat Group Widget controls
      //*****  START GENRAL Filter controls*****//
      $scope.general_stat_filter = {
        custom_range: null,
      };

      $scope.generalFilterChnage = function () {
        console.log("Filter method called");
        console.log($scope.general_stat_filter.custom_range);
      };

      $scope.resetReferrerFilters = function () {
        console.log("Filter @@@ called");
        console.log($scope.general_stat_filter.custom_range);
      };

      //
      $scope.loadStatData = function () {
        console.log("Called");
        Migareference.loadDeps().then(function () {
          // Charts
          // Dummy data for the charts
          var chart1Data = [30, 15, 25, 10, 20];
          var chart2Data = [10, 20, 30, 15, 25];
          var chart3Data = [20, 30, 15, 25, 10];

          // Colors for the data categories
          var colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"];

          // Chart configuration
          var chartConfig = {
            type: "pie",
            data: {
              datasets: [
                {
                  data: [],
                  backgroundColor: colors,
                },
              ],
              labels: [
                "Category 1",
                "Category 2",
                "Category 3",
                "Category 4",
                "Category 5",
              ],
            },
            options: {
              responsive: true,
              legend: {
                display: false,
              },
            },
          };
          console.log("All is ok");
          // Initialize the charts
          var chart1 = new Chart(
            document.getElementById("chart1"),
            chartConfig
          );
          var chart2 = new Chart(
            document.getElementById("chart2"),
            chartConfig
          );
          var chart3 = new Chart(
            document.getElementById("chart3"),
            chartConfig
          );

          // Update the chart data
          chart1.data.datasets[0].data = chart1Data;
          chart2.data.datasets[0].data = chart2Data;
          chart3.data.datasets[0].data = chart3Data;

          // Update the charts
          chart1.update();
          chart2.update();
          chart3.update();
        });
      };
      // $scope.loadStatData();
    }
  );
