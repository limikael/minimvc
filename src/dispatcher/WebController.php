<?php

	require_once __DIR__."/ControllerMethod.php";

	/**
	 * Base application controller.
	 */
	class WebController {

		private $methods=array();
		private $defaultMethod;
		private $defaultType;
		private $dispatcher;

		/**
		 * Construct.
		 */
		public function WebController() {
		}

		/**
		 * Set default method.
		 */
		protected function setDefaultMethod($value) {
			$this->defaultMethod=$value;
		}

		/**
		 * Set reference to dispatcher.
		 */
		public function setDispatcher($value) {
			$this->dispatcher=$value;
		}

		/**
		 * Add method.
		 */
		protected function addMethod($method) {
			if (!$this->methods)
				$this->methods=array();

			$method->setController($this);
			$this->methods[$method->getName()]=$method;
		}

		/**
		 * Set default type.
		 */
		protected function setDefaultType($type) {
			$this->defaultType=$type;
		}

		/**
		 * Get method for invocation.
		 */
		public function getMethod($method) {
			if (!$method)
				$method=$this->defaultMethod;

			if (!array_key_exists($method,$this->methods))
				return NULL;

			return $this->methods[$method];
		}

		/**
		 * Create and add a method.
		 */
		protected function method($methodName) {
			$m=new ControllerMethod($methodName);
			$m->type($this->defaultType);
			$this->addMethod($m);

			return $m;
		}

		/**
		 * Fail.
		 */
		public function fail($message, $trace) {
			$this->dispatcher->fail($message,$trace);
		}
	}