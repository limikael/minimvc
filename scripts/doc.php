#!/usr/bin/env php
<?php

	system(__DIR__."/../vendor/bin/apigen -s ".__DIR__."/../src -d ".__DIR__."/../doc",$ret);
	exit($ret);