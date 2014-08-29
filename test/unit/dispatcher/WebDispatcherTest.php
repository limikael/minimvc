<?php

	require_once __DIR__."/../../../src/dispatcher/WebDispatcher.php";

	class WebDispatcherTest extends \PHPUnit_Framework_TestCase {

		function testBasic() {
			$dispatcher=new WebDispatcher(__DIR__."/../../controller");

			ob_start();
			$dispatcher->dispatchPath("test/stuff");
			$content=ob_get_clean();

			$this->assertEquals('{"hello":"world","ok":1}',$content);
		}

		function testPath() {
			$dispatcher=new WebDispatcher(__DIR__."/../../controller");

			ob_start();
			$dispatcher->dispatchPath("test/twopaths/hello/world");
			$content=ob_get_clean();

			$this->assertEquals('helloworld',$content);
		}
	}