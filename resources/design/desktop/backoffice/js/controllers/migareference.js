App.config(function ($routeProvider) {
  $routeProvider.when(BASE_URL + "/migareference/backoffice_migareference", {
    controller: "MigareferenceController",
    templateUrl: BASE_URL + "/migareference/backoffice_migareference/template",
  });
})
  .controller(
    "MigareferenceController",
    function ($scope, Header, Label, Migareference) {
      $scope.header = new Header();
      $scope.header.button.left.is_visible = false;
      $scope.form_loader_is_visible = false;
      $scope.content_loader_is_visible = false;
      $scope.shortnercredentials = {};
      $scope.license = null;
      $scope.help = null;
      curruntdate = new Date();
      beforedate = curruntdate.setDate(curruntdate.getDate() - 30);
      beforedate = new Date(beforedate);
      $scope.tableStateFilter = { from_date: beforedate, to_date: new Date() };
      $scope.loadLicense = function () {
        Migareference.loadLicense().success(function (data) {
          $scope.header.title = data.title;
          $scope.header.icon = data.icon;
          $scope.license = data;
        });
      };
      $scope.saveShortnercredentials = function () {
        $scope.content_loader_is_visible = true;
        Migareference.saveShortnercredentials($scope.shortnercredentials)
          .success(function (data) {
            var message;
            message = data.message;
            $scope.message.setText(message).isError(false).show();
            location.reload();
          })
          .error(function (data) {
            var message;
            message = data.message;
            $scope.message.setText(message).isError(true).show();
          })
          .finally(function () {
            $scope.content_loader_is_visible = false;
          });
      };
      $scope.saveLicense = function () {
        $scope.form_loader_is_visible = true;
        Migareference.saveLicense($scope.license)
          .success(function (data) {
            var message = Label.save.error;
            if (angular.isObject(data) && angular.isDefined(data.message)) {
              message = data.message;
              $scope.message.isError(false);
            } else {
              $scope.message.isError(true);
            }
            $scope.message.setText(message).show();
            window.location.href = window.location.href;
          })
          .error(function (data) {
            var message = Label.save.error;
            if (angular.isObject(data) && angular.isDefined(data.message)) {
              message = data.message;
            }
            $scope.message.setText(message).isError(true).show();
          })
          .finally(function () {
            $scope.form_loader_is_visible = false;
          });
      };

      $scope.loadHelp = function () {
        Migareference.loadHelp().success(function (data) {
          $scope.help = data;
        });
      };
      //added by imran start
      $scope.ledger_cron_data = [];
      $scope.perPageLedgerCronData = 10;
      $scope.pageLedgerCronData = 0;
      $scope.clientLimitLedgerCronData = 250;
      $scope.urlParamsLedgerCron = {
        filter: "",
        order: "started_at",
        by: false,
      };
      $scope.ledger_data = [];
      $scope.perPageLedgerData = 10;
      $scope.pageLedgerData = 0;
      $scope.clientLimitLedgerData = 250;
      $scope.urlParamsLedger = {
        filter: "",
        order: "app_id",
        by: false,
      };
      $scope.loadMigachainCredentials = function () {
        Migareference.loadMigachainCredentials().success(function (data) {
          $scope.migachain_credentials = data;
        });
      };
      $scope.saveMigachainCredentials = function () {
        $scope.form_loader_migachain_is_visible = true;
        Migareference.saveMigachainCredentials($scope.migachain_credentials)
          .success(function (data) {
            var message = Label.save.error;
            if (angular.isObject(data) && angular.isDefined(data.message)) {
              message = data.message;
              $scope.message.isError(false);
            } else {
              $scope.message.isError(true);
            }
            $scope.message.setText(message).show();
            window.location.href = window.location.href;
          })
          .error(function (data) {
            var message = Label.save.error;
            if (angular.isObject(data) && angular.isDefined(data.message)) {
              message = data.message;
            }
            $scope.message.setText(message).isError(true).show();
          })
          .finally(function () {
            $scope.form_loader_migachain_is_visible = false;
          });
      };
      //added by imran end
      $scope.loadSiberianUserTaxid = function () {
        Migareference.loadSiberianUserTaxid().success(function (data) {
          $scope.default_tax = data;
        });
      };
      $scope.svaeSiberianUserTaxid = function () {
        $scope.form_loader_tax_id_is_visible = true;
        Migareference.svaeSiberianUserTaxid($scope.default_tax)
          .success(function (data) {
            var message = Label.save.error;
            if (angular.isObject(data) && angular.isDefined(data.message)) {
              message = data.message;
              $scope.message.isError(false);
            } else {
              $scope.message.isError(true);
            }
            $scope.message.setText(message).show();
            window.location.href = window.location.href;
          })
          .error(function (data) {
            var message = Label.save.error;
            if (angular.isObject(data) && angular.isDefined(data.message)) {
              message = data.message;
            }
            $scope.message.setText(message).isError(true).show();
          })
          .finally(function () {
            $scope.form_loader_tax_id_is_visible = false;
          });
      };
      $scope.saveHelp = function () {
        $scope.form_loader_help_is_visible = true;
        Migareference.saveHelp($scope.help)
          .success(function (data) {
            var message = Label.save.error;
            if (angular.isObject(data) && angular.isDefined(data.message)) {
              message = data.message;
              $scope.message.isError(false);
            } else {
              $scope.message.isError(true);
            }
            $scope.message.setText(message).show();
            window.location.href = window.location.href;
          })
          .error(function (data) {
            var message = Label.save.error;
            if (angular.isObject(data) && angular.isDefined(data.message)) {
              message = data.message;
            }
            $scope.message.setText(message).isError(true).show();
          })
          .finally(function () {
            $scope.form_loader_help_is_visible = false;
          });
      };

      $scope.loadAppLicenses = function () {
        Migareference.loadAppLicenses().success(function (data) {
          $scope.app_licenses = data.app_licenses;
        });
      };
      $scope.loadtablestats = function () {
        $scope.content_loader_is_visible = true;
        $scope.loadGraph();
        Migareference.loadtablestats(
          $scope.tableStateFilter.from_date,
          $scope.tableStateFilter.to_date
        )
          .success(function (data) {
            $scope.tablestats = null;
            console.log("My data");
            console.log(data);
            $scope.tablestats = data;
            $scope.from_date = data[0].dates.from_date;
            console.log("all is ok");
          })
          .error(function (data) {
            var message;
            message = data.message;
            $scope.message.setText(message).isError(true).show();
          })
          .finally(function () {
            $scope.content_loader_is_visible = false;
          });
      };
      $scope.loadGraph = function () {
        Migareference.loadGraph(
          $scope.tableStateFilter.from_date,
          $scope.tableStateFilter.to_date
        )
          .success(function (data) {
            var stat_data_label = data.stat_data_label;
            var net_refreal = data.net_refreal;
            var total_reports = data.total_reports;
            var active_reports = data.active_reports;
            var declined_reports = data.declined_reports;
            var paid_reports = data.paid_reports;
            var payable_reports = data.payable_reports;
            $scope.cssStyle = "height:420px; width:1140px;";
            var labels = stat_data_label.map(function (stat) {
              return stat[0];
            });
            var net_refreal_data = net_refreal.map(function (stat) {
              return stat[0];
            });
            var total_reports_data = total_reports.map(function (stat) {
              return stat[0];
            });
            var active_reports_data = active_reports.map(function (stat) {
              return stat[0];
            });
            var declined_reports_data = declined_reports.map(function (stat) {
              return stat[0];
            });
            var paid_reports_data = paid_reports.map(function (stat) {
              return stat[0];
            });
            var payable_reports_data = payable_reports.map(function (stat) {
              return stat[0];
            });
            $scope.graphSeries = data.stats_labels;
            $scope.graphLabels = labels;
            $scope.graphData = [
              net_refreal_data,
              total_reports_data,
              active_reports_data,
              declined_reports_data,
              paid_reports_data,
              payable_reports_data,
            ];
            var color = [
              "204,37,41",
              "62, 114, 48 ",
              "4, 4, 4 ",
              "37, 77, 113",
              "102, 113, 37",
              "37, 109, 113",
            ];
            $scope.graphDatasetOverride = [
              {
                borderColor: "rgba(" + color[0] + ",1)",
                backgroundColor: "rgba(" + color[0] + ",0.4)",
                pointBorderColor: "rgba(" + color[0] + ",0.4)",
                pointBackgroundColor: "rgba(" + color[0] + ",1)",
                pointHoverBackgroundColor: "rgba(" + color[0] + ",1)",
                pointHoverBorderColor: "rgba(" + color[0] + ",0.4)",
                type: "line",
                fill: false,
                lineTension: 0,
                hidden: false,
                yAxisID: "new",
              },
              {
                borderColor: "rgba(" + color[1] + ",1)",
                backgroundColor: "rgba(" + color[1] + ",0.4)",
                pointBorderColor: "rgba(" + color[1] + ",0.4)",
                pointBackgroundColor: "rgba(" + color[1] + ",1)",
                pointHoverBackgroundColor: "rgba(" + color[1] + ",1)",
                pointHoverBorderColor: "rgba(" + color[1] + ",0.4)",
                type: "line",
                fill: false,
                hidden: true,
                lineTension: 0,
                yAxisID: "new",
              },
              {
                borderColor: "rgba(" + color[2] + ",1)",
                backgroundColor: "rgba(" + color[2] + ",0.4)",
                pointBorderColor: "rgba(" + color[2] + ",0.4)",
                pointBackgroundColor: "rgba(" + color[2] + ",1)",
                pointHoverBackgroundColor: "rgba(" + color[2] + ",1)",
                pointHoverBorderColor: "rgba(" + color[2] + ",0.4)",
                type: "line",
                fill: false,
                hidden: true,
                lineTension: 0,
                yAxisID: "new",
              },
              {
                borderColor: "rgba(" + color[3] + ",1)",
                backgroundColor: "rgba(" + color[3] + ",0.4)",
                pointBorderColor: "rgba(" + color[3] + ",0.4)",
                pointBackgroundColor: "rgba(" + color[3] + ",1)",
                pointHoverBackgroundColor: "rgba(" + color[3] + ",1)",
                pointHoverBorderColor: "rgba(" + color[3] + ",0.4)",
                type: "line",
                fill: false,
                hidden: true,
                lineTension: 0,
                yAxisID: "new",
              },
              {
                borderColor: "rgba(" + color[4] + ",1)",
                backgroundColor: "rgba(" + color[4] + ",0.4)",
                pointBorderColor: "rgba(" + color[4] + ",0.4)",
                pointBackgroundColor: "rgba(" + color[4] + ",1)",
                pointHoverBackgroundColor: "rgba(" + color[4] + ",1)",
                pointHoverBorderColor: "rgba(" + color[4] + ",0.4)",
                type: "line",
                fill: false,
                hidden: true,
                lineTension: 0,
                yAxisID: "new",
              },
              {
                borderColor: "rgba(" + color[5] + ",1)",
                backgroundColor: "rgba(" + color[5] + ",0.4)",
                pointBorderColor: "rgba(" + color[5] + ",0.4)",
                pointBackgroundColor: "rgba(" + color[5] + ",1)",
                pointHoverBackgroundColor: "rgba(" + color[5] + ",1)",
                pointHoverBorderColor: "rgba(" + color[5] + ",0.4)",
                type: "line",
                fill: false,
                hidden: true,
                lineTension: 0,
                yAxisID: "new",
              },
            ];
            $scope.graphOptions = {
              legend: {
                display: true,
              },
              scales: {
                yAxes: [
                  {
                    afterBuildTicks: function (chartElem) {
                      var ticks = [];
                      for (var i = 0; i < chartElem.ticks.length; i++) {
                        if (chartElem.ticks[i] % 1 === 0) {
                          ticks.push(chartElem.ticks[i]);
                        }
                      }
                      chartElem.ticks = ticks;
                      if (chartElem.start < 0) {
                        chartElem.start = 0;
                      }
                    },
                    max: 100,
                    id: "new",
                    type: "linear",
                    display: true,
                    position: "left",
                    beginAtZero: true,
                  },
                ],
              },
            };
          })
          .error(function (data) {
            var message;
            message = data.message;
            $scope.message.setText(message).isError(true).show();
          })
          .finally(function () {
            // $scope.content_loader_is_visible = false;
          });
      };
      $scope.loadLicense();
      $scope.loadHelp();
      $scope.loadMigachainCredentials(); //added by imran
      $scope.loadSiberianUserTaxid();
      $scope.loadAppLicenses();
      $scope.loadtablestats();
      $scope.loadGraph();
    }
  )
  .directive("exportToCsv", function () {
    return {
      restrict: "A",
      link: function (scope, element, attrs) {
        var el = element[0];
        element.bind("click", function (e) {
          var table = document.getElementById("table");
          var csvString = "";          
          for (var i = 0; i < table.rows.length; i++) {
            var rowData = table.rows[i].cells;            
            for (var j = 0; j < rowData.length; j++) {
              csvString = csvString +rowData[j].innerHTML.replace(/-/g, ' ').replace(/_/g, ' ').replace(/&/g, ' ').replace(/\./g, '') + ",";
            }
            csvString = csvString.substring(0, csvString.length - 1);
            csvString = csvString + "\n";
            console.log(i);
            btoa(csvString);
          }
          csvString = csvString.substring(0, csvString.length - 1);
          var a = $("<a/>", {
            style: "display:none",
            href: "data:application/octet-stream;base64," + btoa(csvString),
            download: "ReportStatistics.csv",
          }).appendTo("body");
          a[0].click();
          a.remove();
        });
      },
    };
  });