<?php

	require_once __DIR__."/../../src/dispatcher/WebController.php";

	/**
	 * Test controller.
	 */
	class TestController extends WebController {

		/**
		 * Construct.
		 */
		public function TestController() {
			//$this->setDefaultType("json");

			$this->method("stuff")->type("json");
			$this->method("twopaths")->paths(2);
			$this->method("generror");
			$this->method("genjsonerror")->type("json");
		}

		/**
		 * Stuff.
		 */
		public function stuff() {
			return array(
				"hello"=>"world"
			);
		}

		/**
		 * With 2 paths.
		 */
		public function twopaths($a,$b) {
			echo $a.$b;
		}

		/**
		 * Gen error.
		 */
		public function generror() {
			throw new Exception("this is an error");
		}

		/**
		 * Gen error.
		 */
		public function genjsonerror() {
			throw new Exception("this is an error");
		}
	}
