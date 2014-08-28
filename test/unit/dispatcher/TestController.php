<?php

	/**
	 * Test controller.
	 */
	class TestController  {

		/**
		 * Construct.
		 */
		public function TestController() {
			$this->setDefaultMethodType("json");

			$this->method("stuff")->paths(1)->args("hello","world")->type("json");
		}

	}
