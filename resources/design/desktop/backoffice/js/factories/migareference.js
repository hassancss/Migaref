App.factory("Migareference", function ($http, Url) {
  var factory = {};

  factory.loadLicense = function () {
    return $http({
      method: "GET",

      url: Url.get("migareference/public_license/validate"),

      cache: true,

      responseType: "json",
    });
  };

  factory.saveLicense = function (license) {
    return $http({
      method: "POST",

      data: license,

      url: Url.get("migareference/public_license/save"),

      cache: false,

      responseType: "json",
    });
  };

  factory.saveShortnercredentials = function (credential) {
    return $http({
      method: "POST",

      data: credential,

      url: Url.get(
        "migareference/backoffice_migareference/saveurlshortnercredentials"
      ),

      cache: false,

      responseType: "json",
    });
  };

  factory.loadHelp = function () {
    return $http({
      method: "GET",

      url: Url.get("migareference/backoffice_migareference/help"),

      cache: true,

      responseType: "json",
    });
  };

  factory.saveHelp = function (help) {
    return $http({
      method: "POST",

      data: help,

      url: Url.get("migareference/backoffice_migareference/savehelp"),

      cache: false,

      responseType: "json",
    });
  };

  factory.loadAppLicenses = function () {
    return $http({
      method: "GET",

      url: Url.get("migareference/public_license/applicenses"),

      cache: true,

      responseType: "json",
    });
  };

  factory.loadGraph = function (from_date, to_date) {
    return $http({
      method: "GET",

      url: Url.get("migareference/backoffice_migareference/loadgraph", {
        from_date: from_date,
        to_date: to_date,
      }),

      cache: true,

      responseType: "json",
    });
  };

  factory.loadtablestats = function (from_date, to_date) {
    return $http({
      method: "GET",

      url: Url.get("migareference/backoffice_migareference/loadtablestats", {
        from_date: from_date,
        to_date: to_date,
      }),

      cache: true,

      responseType: "json",
    });
  };

  //added by imran start

  factory.loadMigachainCredentials = function () {
    return $http({
      method: "GET",

      url: Url.get(
        "migareference/backoffice_migareference/loadmigachaincredentials"
      ),

      cache: true,

      responseType: "json",
    });
  };

  factory.saveMigachainCredentials = function (migachain_credentials) {
    return $http({
      method: "POST",

      data: migachain_credentials,

      url: Url.get(
        "migareference/backoffice_migareference/savemigachaincredentials"
      ),

      cache: false,

      responseType: "json",
    });
  };

  //added by imran end

  factory.loadSiberianUserTaxid = function () {
    return $http({
      method: "GET",

      url: Url.get(
        "migareference/backoffice_migareference/loadsiberianusertaxid"
      ),

      cache: true,

      responseType: "json",
    });
  };

  factory.svaeSiberianUserTaxid = function (default_tax) {
    return $http({
      method: "POST",

      data: default_tax,

      url: Url.get(
        "migareference/backoffice_migareference/svaesiberianusertaxid"
      ),

      cache: false,

      responseType: "json",
    });
  };

  return factory;
});
