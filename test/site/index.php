<?php

	require_once __DIR__."/../../src/dispatcher/WebDispatcher.php";

	$dispatcher=new WebDispatcher(__DIR__."/../controller");
	$dispatcher->dispatch();