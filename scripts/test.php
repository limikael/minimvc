#!/usr/bin/env php
<?php

	system(__DIR__."/../vendor/bin/phpunit ".__DIR__."/../test/unit",$ret);
	exit($ret);