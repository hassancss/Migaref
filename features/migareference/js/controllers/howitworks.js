angular
  .module("starter")
  .controller(
    "MigareferencehowitworksController",
    function (
      Loader,
      $sce,
      $scope,
      $state,
      $window,
      $timeout,
      $stateParams,
      Migareference,
      Customer,
      LinkService
    ) {
      angular.extend($scope, {
        is_loading: true,
        value_id: $stateParams.value_id,
        gethowto: null,
        video_url: {},
        site_link: "",
        pdf_url: "",
        is_video: "",
        contact_us_link: "",
        is_video_link: false,
        is_how_to_text: false,
        is_video_souce: false,
        is_site_link: false,
        is_email: false,
        is_phone: false,
        contact_us_email: "",
        contact_us_phone: "",
        how_to_text: "",
        home_icos: null,
        video_height: "",
      });
      $scope.desc =
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec rutrum vehicula tortor, vitae ornare nunc semper eu. Vivamus varius, eros vel tristique accumsan, libero nulla cursus ante, eu eleifend risus orci scelerisque nibh. Curabitur feugiat, augue ut commodo bibendum, nisi leo porttitor diam, tincidunt auctor tellus ante sit amet nibh. Duis velit libero, aliquam at felis eu, pellentesque mollis mi. Nam a est orci. Ut bibendum sagittis semper. Cras eget arcu non augue mollis aliquam. Ut ut gravida ligula. Nulla imperdiet lacinia mi, nec fringilla mauris interdum at. Phasellus gravida tempor varius. Cras molestie et nulla eget maximus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris aliquet malesuada feugiat. Curabitur fermentum bibendum nulla, non dictum ipsum tincidunt non. Quisque convallis pharetra tempor. Donec id pretium leo. Pellentesque luctus massa non elit viverra pellentesque. Cras vitae neque molestie, rhoncus ipsum sit amet, lobortis dui. Fusce in urna sem. Vivamus vehicula dignissim augue et scelerisque. Etiam quam nisi, molestie ac dolor in, tincidunt tincidunt arcu. Praesent sed justo finibus, fringilla velit quis, porta erat. Donec blandit metus ut arcu iaculis iaculis. Cras nec dolor fringilla justo ullamcorper auctor. Aliquam eget pretium velit. Morbi urna justo, pulvinar id lobortis in, aliquet placerat orci.";
      $scope.migayoutube = {};
      $scope.migayoutube.collection = {};
      // GO to Home Page
      $scope.goToHomePage = function () {
        $state.go("home");
      };

      $scope.call = function () {
        LinkService.openLink("tel:" + $scope.contact_us_phone, {}, true);
      };

      $scope.email = function () {
        $window.open("mailto:" + $scope.contact_us_email, "_system");
      };
      $scope.goToSiteUrl = function () {
        window.open($scope.gethowto.site_link, "_system", "location=yes");
      };
      $scope.directorPhone = function () {
        LinkService.openLink("tel:" + $scope.gethowto.director_phone, {}, true);
      };

      $scope.directorEmail = function () {
        $window.open("mailto:" + $scope.gethowto.director_email, "_system");
      };
      $scope.directorCalendarUrl = function () {
        window.open($scope.gethowto.director_calendar_url, "_system", "location=yes");
      };
      $scope.showIt =
        "<iframe width='560' height='315' src='https://www.youtube.com/embed/NEP_zOD7ZLM?controls=0' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
      $scope.htmlSafe = function (data) {
        return $sce.trustAsHtml(data);
      };
      $scope.getHowto = function () {
        $scope.is_loading = true;
        Loader.show();
        Migareference.getHowto(Customer.customer.id)
          .success(function (data) {
            $scope.gethowto = data.gethowto;
            $scope.unit = $scope.gethowto.how_to_source_unit;
            $scope.height = $scope.gethowto.frame_height;
            var t = $scope.height.toString();
            if ($scope.unit == "px") {
              $scope.fm_height = t + "px";
            } else {
              $scope.fm_height = t + "%";
            }
            console.log($scope.fm_height);

            $scope.how_to_text = $sce.trustAsHtml(data.gethowto.how_to_text);
            // $scope.video_url= $sce.trustAsResourceUrl($scope.gethowto.video_link);
            $scope.how_to_video_source = $sce.trustAsHtml(
              $scope.gethowto.how_to_video_source
            );
            $scope.contact_us_email = $sce.trustAsResourceUrl(
              $scope.gethowto.contact_us_email
            );
            $scope.contact_us_phone = $sce.trustAsResourceUrl(
              $scope.gethowto.contact_us_phone
            );
            if ($scope.gethowto.video_link != "") {
              $scope.is_video_link = true;
              $scope.video_url = data.collection;
              $scope.migayoutube.collection = data.collection.videos;
            }
            if ($scope.gethowto.how_to_text != "") {
              $scope.is_how_to_text = true;
            }
            if ($scope.gethowto.how_to_video_source != "") {
              $scope.is_video_souce = true;
            }
            if ($scope.gethowto.site_link != "") {
              $scope.is_site_link = true;
            }
            if ($scope.gethowto.contact_us_email != "") {
              $scope.is_email = true;
            }
            if ($scope.gethowto.contact_us_phone != "") {
              $scope.is_phone = true;
            }
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
      $scope.tooltipVisible = false;
      $scope.copyReferralLink = function () {

        // Create a temporary input element to hold the text to copy
        var tempInput = document.createElement('input');
        tempInput.value = $scope.gethowto.referrer_link;
        document.body.appendChild(tempInput);

        // Select and copy the text
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        // Show the tooltip
        $scope.tooltipVisible = true;

        // Hide the tooltip after 2 seconds
        $timeout(function() {
          $scope.tooltipVisible = false;
        }, 2000);
      };
      $scope.userType = function () {
        Migareference.userType(Customer.customer.id)
          .success(function (data) {
            $scope.is_admin = data.is_admin;
            $scope.is_admin_agent_group = data.is_admin_agent_group;
            $scope.is_agent = data.is_agent;
          })
          .error(function () {
            // $scope.is_loading = false;
          })
          .finally(function () {
            // $scope.is_loading = false;
          });
      };
      $scope.userType();
      $scope.loadHomecontent();
      $scope.getHowto();
    }
  );
