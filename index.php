<?php
	session_start(['name'=>'STR']);
	require_once "./Core/generalConfig.php";
	require_once "./Helpers/Helpers.php";
	require_once "./Controllers/viewControllers.php";
	$view = new viewControllers();
	$view->get_controller_view();
  

	
