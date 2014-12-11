#!/usr/bin/env php
<?php

	system("zip doc.zip -r doc", $ret);

	if ($ret)
		exit("Unable to run zip");

	$res=system("curl -s -X POST --data-binary @doc.zip 'http://limikael.altervista.org/?target=minimvcdoc&key=4Lwr4C8Y'", $ret);

	echo "\n";

	if ($ret)
		exit("Unable to publish doc");

	if ($res!="OK")
		exit("Unable to publish doc");
