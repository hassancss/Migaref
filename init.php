<?php

$init = function($bootstrap) {

  	# Ownerend activation menu under analytics menu
	\Siberian\Hook::listen("editor.left.menu.ready", "Listening editor ok", function ($payload) {
		$canAccess = function($acl) {
			$aclList = \Admin_Controller_Default::_getAcl();
			if ($aclList) {
				return $aclList->isAllowed($acl);
			}
			return true;
		};
		$currentUrl = str_replace(\Core\Model\Base::getBaseUrl(), "", \Core\Model\Base::getCurrentUrl());
		$payload["analytics"]["childs"]["migareference"] = [
			"hasChilds" => false,
			"isVisible" => $canAccess("analytics_migareference"),
			"label" => __("Migareference"),
			"icon" => "fa fa-line-chart",
			"url" => __path("/migareference/generalstats/general"),
			"is_current" => (preg_match("#^/migareference/generalstats/general#", $currentUrl)),
		];
		return $payload;
	}, 0);


    # Backoffice activation menu
    Siberian_Module::addMenu(
		'Migareference',
		'migareference',
		'Migareference',
		'migareference/backoffice_migareference',
		'fa fa-home'
	);

	//get the base path
	$base = Core_Model_Directory::getBasePathTo("/app/local/modules/Migareference/");
	# cryptor
	require_once "{$base}/libs/cryptor.php";
	# MPDF
	require_once "{$base}/libs/mpdf/mpdf.php";
};
