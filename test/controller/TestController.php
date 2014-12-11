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
			$this->method("generror2");
			$this->method("genjsonerror2")->type("json");
			$this->method("def");
			$this->method("error");

			$this->setDefaultMethod("def");
		}

		/**
		 * Default.
		 */
		public function def() {
			echo "default...";
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

		/**
		 * Gen error.
		 */
		public function generror2() {
			$t=array();
			$a=$t["hello"];
//			throw new Exception("this is an error");
		}

		/**
		 * Gen error.
		 */
		public function genjsonerror2() {
			$t=array();
			$a=$t["hello"];
//			throw new Exception("this is an error");
		}

		/**
		 *
		 */
		public function error($e) {
			echo "there is an error: ".$e->getMessage();
		}
	}
