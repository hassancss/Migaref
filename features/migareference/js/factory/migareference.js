angular
  .module("starter")
  .factory(
    "Migareference",
    function ($rootScope, $http, $q, Url, $pwaRequest, $ocLazyLoad) {
      var factory = {};
      factory.value_id = null;
      factory.pre_settings = {};
      factory.app_content = {};
      factory.user_type = {};
      factory.app_full_version = "4.0.11";
      factory.app_short_version = "4.11";
      // Promise-based deps loader!

      factory.load = function (value_id, platform) {
        if (!value_id) return;
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/load", {
            value_id: value_id,
            app_short_version: factory.app_short_version,
            platform: platform,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.loadDeps = function () {
        var charts = $q.resolve(); // Resolve by default!
        charts = $ocLazyLoad.load(["https://cdn.jsdelivr.net/npm/chart.js"]);
        // All promises are resolved, we can load the page!
        return $q.all([charts]);
      };
      factory.loadLedger = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/loadledger", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.userType = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/usertype", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.saveSharelogs = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/savesharelogs", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getAgent = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getagent", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.loadDaybook = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/loaddaybook", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.markAsDone = function (log_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/markasdone", {
            log_id: log_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getAiMatching = function (referrer_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_phonebook/getaimatching", {
            referrer_id: referrer_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.refreshAiMatching = function (phonebook_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_phonebook/refreshaimatching", {
            phonebook_id: phonebook_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.matchCustomer = function (matching_network_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_phonebook/matchcustomer", {
            matching_network_id: matching_network_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.discardCustomer = function (matching_network_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_phonebook/discardcustomer", {
            matching_network_id: matching_network_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.removeCustomer = function (matching_network_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_phonebook/removecustomer", {
            matching_network_id: matching_network_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.matchCustomer = function (matching_network_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_phonebook/matchcustomer", {
            matching_network_id: matching_network_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.unMatchCustomer = function (matching_network_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_phonebook/unmatchcustomer", {
            matching_network_id: matching_network_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.transferReferrer = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/transferreferrer", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getprovince = function (country_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getprovince", {
            country_id: country_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.loadprovinces = function (country_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/loadprovinces", {
            country_id: country_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.editNote = function (public_key) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/editnote", {
            public_key: public_key,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.editReminder = function (public_key) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/editreminder", {
            public_key: public_key,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.deleteNote = function (public_key) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/deletenote", {
            public_key: public_key,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.deleteReminder = function (public_key) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/deletereminder", {
            public_key: public_key,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.updateReminder = function (
        public_key,
        report_id,
        user_id,
        status,
        changedValue
      ) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/updatereminder", {
            public_key: public_key,
            report_id: report_id,
            user_id: user_id,
            status: status,
            changevalue: changedValue,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.updateAutoReminder = function (
        public_key,
        user_id,
        status,
        changed_value,
        phobebook_id
      ) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/updateautoreminder", {
            public_key: public_key,
            user_id: user_id,
            status: status,
            changed_value: changed_value,
            phobebook_id: phobebook_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.saveNote = function (notefield) {
        return $pwaRequest.post("migareference/mobile_view/savenote", {
          data: notefield,
          cache: false,
        });
      };
      factory.saveReminder = function (notefield) {
        return $pwaRequest.post("migareference/mobile_view/savereminder", {
          data: notefield,
          cache: false,
        });
      };
      factory.saveManualConsent = function (manualconsent) {
        return $pwaRequest.post("migareference/mobile_view/savemanualconsent", {
          data: manualconsent,
          cache: false,
        });
      };
      factory.saveNewUser = function (notefield) {
        return $pwaRequest.post("migareference/mobile_view/savenewuser", {
          data: notefield,
          cache: false,
        });
      };
      factory.addCoommunicationLog = function (addlogitem) {
        return $pwaRequest.post(
          "migareference/mobile_view/addcoommunicationlog",
          {
            data: addlogitem,
            cache: false,
          }
        );
      };
      factory.addNewJob = function (jobitem) {
        return $pwaRequest.post("migareference/mobile_view/addnewjob", {
          data: jobitem,
          cache: false,
        });
      };
      factory.deleteCommunicationLog = function (deleteitem) {
        return $pwaRequest.post(
          "migareference/mobile_view/deletecommunicationlog",
          {
            data: deleteitem,
            cache: false,
          }
        );
      };
      factory.deleteReferrer = function (deleteitem) {
        return $pwaRequest.post("migareference/mobile_view/deletereferrer", {
          data: deleteitem,
          cache: false,
        });
      };
      factory.saveNewPhone = function (jobtitle) {
        return $pwaRequest.post("migareference/mobile_view/savenewphone", {
          data: jobtitle,
          cache: false,
        });
      };
      factory.getNotifications = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getnotifications", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.buildmessage = function (user_id, agent_id, report_by, type, report_custom_type) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/buildmessage", {
            user_id: user_id,
            agent_id: agent_id,
            report_by: report_by,
            type: type,
            report_custom_type: report_custom_type,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.showNotesList = function (report_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/shownoteslist", {
            report_id: report_id
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getCallScript = function (phobebook_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_openai/getcallscript", {
            phobebook_id: phobebook_id
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getreportreminders = function (customer_id, filter_key) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getreportreminders", {
            customer_id: customer_id,
            filter_key: filter_key,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getreferrerreminders = function (customer_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getreferrerreminders", {
            customer_id: customer_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.totalCredits = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/totalcredits", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.fetchsettings = function () {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/fetchsettings"),
          cache: false,
          responseType: "json",
        });
      };
      factory.loadAgentGroup = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/loadagentgroup", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.loadHomecontent = function () {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/loadhomecontent"),
          cache: false,
          responseType: "json",
        });
      };
      factory.saveLog = function (data) {
        return $pwaRequest.post("migareference/mobile_view/savelog", {
          data: data,
          cache: false,
        });
      };
      factory.getprizelist = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getprizelist", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getnoteslist = function (report_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getnoteslist", {
            report_id: report_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getexternallinks = function (report_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getexternallinks", {
            report_id: report_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getreportremindertype = function (report_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getreportremindertype"),
          cache: false,
          responseType: "json",
        });
      };
      factory.getreminderlist = function (report_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getsinglereportreminder", {
            report_id: report_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getfiltericon = function (report_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getfiltericon", {
            report_id: report_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      // Deprecated method now the method is devided 
      // factory.getphonebooks = function (rating, prospectrating, user_id) {
      //   return $http({
      //     method: "GET",
      //     url: Url.get("migareference/mobile_view/getphonebooks", {
      //       rating: rating,
      //       prospectrating: prospectrating,
      //       user_id: user_id,
      //     }),
      //     cache: false,
      //     responseType: "json",
      //   });
      // };
      factory.getReferrerPhonebook = function (rating, user_id, currentPage, recordsPerPage) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getreferrerphonebook", {
            rating: rating,
            user_id: user_id,
            currentPage,
            recordsPerPage
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getProspectPhonebook = function (rating, user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getprospectphonebook", {
            rating: rating,
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getredeemprizelist = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getredeemprizelist", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.loadPrizeitem = function (prize_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/loadprizeitem", {
            prize_id: prize_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.loadRedeemprizeitem = function (user_id, prize_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/loadredeemprizeitem", {
            user_id: user_id,
            prize_id: prize_id,
            version: factory.app_short_version,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getHowto = function (customer_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/gethowto", {
            customer_id: customer_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.prereportsettigns = function () {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/prereportsettigns"),
          cache: false,
          responseType: "json",
        });
      };
      factory.loadJobslist = function () {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/loadjobslist"),
          cache: false,
          responseType: "json",
        });
      };
      factory.loadjobs = function () {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/loadjobslist"),
          cache: false,
          responseType: "json",
        });
      };
      factory.reportsettings = function (user_id, type, version) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/reportsettings", {
            user_id: user_id,
            report_type: type,
            version: factory.app_short_version,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getrules = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getpropertysettings", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getPropertysettings = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getpropertysettings", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.savePropertysettings = function (formdata) {
        return $pwaRequest.post(
          "migareference/mobile_view/savepropertysettings",
          {
            data: formdata,
            cache: false,
          }
        );
      };
      factory.savePhoneDetail = function (formdata) {
        return $pwaRequest.post("migareference/mobile_view/savephonedetail", {
          data: formdata,
          cache: false,
        });
      };
      factory.savePhoneEntry = function (formdata) {
        return $pwaRequest.post("migareference/mobile_view/savephoneentry", {
          data: formdata,
          cache: false,
        });
      };
      factory.loadactivereports = function (filters) {
        return $pwaRequest.post(
          "migareference/mobile_view/loadactivereports",
          {
            data: filters,
            cache: false,
          }
        );
      };
      factory.loadeReportdata = function (report_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/loadereportdata", {
            report_id: report_id,
            version: factory.app_short_version,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getphonebookdetail = function (phobebook_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/getphonebookdetail", {
            phobebook_id: phobebook_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.redeemprize = function (prize_id, user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/redeemprize", {
            prize_id: prize_id,
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.chrckMandate = function (pkid, report_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/chrckmandate", {
            pkid: pkid,
            report_id: report_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.savePropertyreport = function (formdata) {
        return $pwaRequest.post(
          "migareference/mobile_view/savepropertyreport",
          {
            data: formdata,
            cache: false,
          }
        );
      };
      factory.updatePropertyreport = function (formdata) {
        return $pwaRequest.post(
          "migareference/mobile_view/updatepropertyreport",
          {
            data: formdata,
            cache: false,
          }
        );
      };
      factory.saveNotes = function (formdata) {
        return $pwaRequest.post("migareference/mobile_view/savenote", {
          data: formdata,
          cache: false,
        });
      };

      factory.loadLedger = function (user_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_view/loadledger", {
            user_id: user_id,
          }),
          cache: false,
          responseType: "json",
        });
      };
      factory.getAppContentTwodata = function (value_id) {
        return $http({
          method: "GET",
          url: Url.get("migareference/mobile_qualification/getappcontentdata", {
            value_id: value_id,
          }),
          cache: false,
          responseType: "json",
        });
      };


      return factory;
    }
  );
