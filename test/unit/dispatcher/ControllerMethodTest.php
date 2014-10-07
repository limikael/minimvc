<?php

	require_once __DIR__."/../../../src/dispatcher/ControllerMethod.php";

	class DummyController {
		public $testCalled=FALSE;
		public $a;
		public $b;

		function test() {
			$this->testCalled=TRUE;
		}

		function afunc($a,$b) {
			$this->a=$a;
			$this->b=$b;
		}

		function anyfunc($p) {
			$this->a=$p["hello"];
			$this->b=$p["world"];
		}
	}

	class ControllerMethodTest extends \PHPUnit_Framework_TestCase {

		function testBasic() {
			$controller=new DummyController();

			$m=new ControllerMethod("test");
			$m->setController($controller);
			$m->invoke(array(),array());

			$this->assertTrue($controller->testCalled);
		}

		function testArgs() {
			$controller=new DummyController();

			$m=new ControllerMethod("afunc");
			$m->args("hello","world");
			$m->setController($controller);
			$m->invoke(array(),array("hello"=>1,"world"=>2));

			$this->assertEquals(1,$controller->a);
		}

		function testAny() {
			$controller=new DummyController();

			$m=new ControllerMethod("anyfunc");
			$m->any();
			$m->setController($controller);
			$m->invoke(array(),array("hello"=>1,"world"=>2));

			$this->assertEquals(1,$controller->a);
			$this->assertEquals(2,$controller->b);
		}
	}