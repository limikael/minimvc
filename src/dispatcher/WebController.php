<?php

	require_once "dispatcher/ControllerMethod.php";

	/**
	 * Base application controller.
	 */
	class WebController {

		private $methods=array();
		private $defaultMethod;
		protected $resultProcessing;

		/**
		 * Construct.
		 */
		public function WebController($resultProcessing=NULL) {
			$this->resultProcessing=$resultProcessing;
		}

		/**
		 * Set default method.
		 */
		protected function setDefaultMethod($value) {
			$this->defaultMethod=$value;
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
		 * Add a method to serve.
		 */
		protected function serve($methodName, $requestParameters=NULL) {
			if (!$requestParameters)
				$requestParameters=array();

			$m=new ControllerMethod($methodName,0,$requestParameters);
			$m->setResultProcessing($this->resultProcessing);
			$this->addMethod($m);
		}

		/**
		 * Add a method to serve.
		 */
		protected function serveWithArgs($methodName, $numPathArgs, $requestParameters=NULL) {
			if (!$requestParameters)
				$requestParameters=array();

			$m=new ControllerMethod($methodName,$numPathArgs,$requestParameters);
			$m->setResultProcessing($this->resultProcessing);
			$this->addMethod($m);
		}

		/**
		 * Set result processing for method.
		 */
		protected function process($methodName, $resultProcessing) {
			if (!array_key_exists($methodName,$this->methods))
				throw new Exception("Unable to set method processing, no method named ".$methodName);

			$this->methods[$methodName]->setResultProcessing($resultProcessing);
		}
	}