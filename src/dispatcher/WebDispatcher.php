<?php

	require_once __DIR__."/../utils/RewriteUtil.php";
	require_once __DIR__."/../template/Template.php";
	require_once __DIR__."/../utils/SystemUtil.php";
	require_once __DIR__."/WebController.php";

	/**
	 * Dispatch controller access.
	 */
	class WebDispatcher {

		private $classPath;
		private $defaultController;
		private $errorTemplate;

		/**
		 * Construct.
		 */
		public function WebDispatcher($classPath) {
			$this->classPath=$classPath;
			$this->errorTemplate=__DIR__."/errortemplate.php";
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
			$this->init();

			if (sizeof($components)>=1)
				$controllerName=$components[0];

			else
				$controllerName=$this->defaultController;

			if (!$controllerName)
				$this->fail("No controller.");

			if (sizeof($components)>=2)
				$methodName=$components[1];

			else
				$methodName="";

			$controllerClassName=ucfirst($controllerName)."Controller";
			$controllerFileName=$this->classPath."/".$controllerClassName.".php";

			try {
				require_once $controllerFileName;
				$controller=new $controllerClassName;
			}

			catch (Exception $e) {
				$this->fail($e->getMessage(),$e->getTraceAsString());
			}

			if (!$controller)
				$this->fail("No such controller.");

			$controller->setDispatcher($this);
			$method=$controller->getMethod($methodName);

			if (!$method)
				$this->fail("No such method.");

			$method->invoke(array_slice($components,2),$_REQUEST);
		}

		/**
		 * Make the system sane.
		 */
		private function init() {
			SystemUtil::enableErrorExceptions();
			SystemUtil::disableMagicQuotes();
		}

		/**
		 * Fail.
		 */
		public function fail($message, $trace="") {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

			$m="**** ".$_SERVER["HTTP_HOST"]." ****\n\n$message\n\n$trace";

			$m=nl2br($m);

			$t=new Template($this->errorTemplate);
			$t->set("message",$m);
			$t->show();
			exit();
		}
	}