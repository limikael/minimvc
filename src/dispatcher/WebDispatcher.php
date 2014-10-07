<?php

	require_once __DIR__."/../utils/RewriteUtil.php";
	require_once __DIR__."/../template/Template.php";
	require_once __DIR__."/../utils/SystemUtil.php";
	require_once __DIR__."/WebController.php";

	/**
	 * This is the main mechanism to dispatch a web request to a specific method
	 * of a specific controller.
	 * 
	 * For example, if we have a site deployed
	 * to localhost, and someone accesses the following url:
	 *
	 * http://localhost/hello/world
	 *
	 * Then `hello` would be considered the controller and `world` the method.
	 *
	 * This class is used as the main entry point for this mechanism. In order to
	 * use it, create an `index.php` file containing code like this:
	 *
	 * <code>
	 *   <?php
	 *
	 *       require_once "dispatcher/WebDispatcher.php";
	 *
	 *       $dispatcher=new WebDispatcher(_PATH_TO_CONTROLLER_DIR_);
	 *       $dispatcher->dispatch();
	 * </code>
	 *
	 * Where `_PATH_TO_CONTROLLER_DIR_` is the path to your controller classes.
	 * In the case above, the WebDispatcher will search in this directory for
	 * a file called `HelloController.php` and expect to find in it a class
	 * called `HelloController` which should extend the {@link WebController} base class.
	 *
	 * This class should have a registered method called `world`, which will in this case
	 * be invoked. See the {@link WebController} documentation for information on how
	 * to register methods for controllers.
	 *
	 * @see WebController
	 */
	class WebDispatcher {

		private $classPath;
		private $defaultController;
		private $errorTemplate;

		/**
		 * Construct a WebDispatcher.
		 *
		 * @param string $classPath The path where to look for controllers.
		 */
		public function WebDispatcher($classPath) {
			$this->classPath=$classPath;
			$this->errorTemplate=__DIR__."/errortemplate.php";
		}

		/**
		 * Set default controller.
		 *
		 * This specifies which controller that should be invoked if there is no controller
		 * specified in the dispatched url. For this to work, the named controller
		 * needs to also have a default method specified.
		 *
		 * @param string $value The name of the controller that should act as default controller.
		 */
		public function setDefaultController($value) {
			$this->defaultController=$value;
		}

		/**
		 * Dispatch path.
		 *
		 * Call the controller and method as if the specified string would have been the url. 
		 *
		 * @param string $s The url.
		 */
		public function dispatchPath($s) {
			$components=RewriteUtil::splitUrlPath($s);
			$this->dispatchComponents($components);
		}

		/**
		 * Dispatch. Call the requested controller method.
		 *
		 * This function will parse the url to see which controller and method that
		 * should be called, and then call that method.
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
		 *
		 * Output an error header and show error message.
		 *
		 * @param string $message The message to show.
		 * @param string $trace An optional stack trace that will be shown together
		 *        with the message.
		 */
		public function fail($message, $trace="") {
			if (!headers_sent())
				header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

			$m="**** ".$_SERVER["HTTP_HOST"]." ****\n\n$message\n\n$trace";

			$m=nl2br($m);

			$t=new Template($this->errorTemplate);
			$t->set("message",$m);
			$t->show();
			exit();
		}
	}