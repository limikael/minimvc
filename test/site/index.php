<?php

	require_once __DIR__."/../../src/dispatcher/WebDispatcher.php";

	function onError($e) {
		echo "there is an error: ".$e->getMessage();
	}

	$dispatcher=new WebDispatcher(__DIR__."/../controller");
	$dispatcher->setErrorRoute("test/error");
	$dispatcher->setDefaultController("test");
	$dispatcher->dispatch();