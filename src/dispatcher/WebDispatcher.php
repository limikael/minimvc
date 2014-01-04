<?php

	require_once "utils/RewriteUtil.php";

	/**
	 * Dispatch controller access.
	 */
	class WebDispatcher {

		private $classPath;
		private $defaultController;

		/**
		 * Construct.
		 */
		public function WebDispatcher($classPath) {
			$this->classPath=$classPath;
		}

		/**
		 * Set default controller.
		 */
		public function setDefaultController($value) {
			$this->defaultController=$value;
		}

		/**
		 * Dispatch path.
		 */
		public function dispatchPath($s) {
			$components=RewriteUtil::splitUrlPath($s);
			$this->dispatchComponents($components);
		}

		/**
		 * Dispatch.
		 */
		public function dispatch() {
			$components=RewriteUtil::getPathComponents();
			$this->dispatchComponents($components);
		}

		/**
		 * Dispatch components.
		 */
		private function dispatchComponents($components) {
			if (sizeof($components)>=1)
				$controllerName=$components[0];

			else
				$controllerName=$this->defaultController;

			if (!$controllerName)
				$this->prematureFail("No contrller.");

			if (sizeof($components)>=2)
				$methodName=$components[1];

			else
				$methodName="";

			$controllerClassName=ucfirst($controllerName)."Controller";
			$controllerFileName=$this->classPath."/".$controllerClassName.".php";

			require_once $controllerFileName;
			$controller=new $controllerClassName;

			if (!$controller)
				$this->prematureFail("No such controller.");

			$method=$controller->getMethod($methodName);

			if (!$method)
				$this->prematureFail("No such method.");

			$method->invoke(array_slice($components,2),$_REQUEST);
		}

		/**
		 * Fail.
		 */
		private function prematureFail($message) {
			echo $message;
			exit();
		}
	}