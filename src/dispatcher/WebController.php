<?php

	require_once __DIR__."/ControllerMethod.php";

	/**
	 * This is a base class for a controller.
	 *
	 * Classes that should serve as controllers should inherit from this class.
	 * For example, the following code creates a controller called `HelloController`
	 * and registers a method called `world`. This method takes a parameter called
	 * `param`.
	 *
	 * <code>
	 *   class HelloController extends WebController {
	 *       public function HelloController() {
	 *           $this->method("world")->args("param");
	 *       }
	 *
	 *       public function world($param) {
	 *           echo "hello, the parameter is $param";
	 *       }
	 *   }
	 * </code>
	 *
	 * Now, provided that we have a {@link WebDispatcher} set up to invoke the controller,
	 * we are ready to handle requests like:
	 *
	 * http://localhost/hello/world/?param=something
	 *
	 * The value returned by {@link method} is an instance of the {@link ControllerMethod}
	 * class. This class contains chainable methods for specifying details about our
	 * methods. A method specification might look like:
	 *
	 * <code>
	 *     $this->method("getuserdata")->paths(1)->args("domain","group")->any()->type("json");
	 * </code>
	 *
	 * For details about the methods to use for these specifications, see the 
	 * {@link ControllerMethod} class.
	 *
	 * @see WebDispatcher
	 * @see ControllerMethod
	 */
	class WebController {

		private $methods=array();
		private $defaultMethod;
		private $defaultType;
		private $dispatcher;

		/**
		 * Constructor.
		 *
		 * This is not intended to be invoked by user applications, the invokation
		 * of controllers is handled by the system.
		 */
		public function WebController() {
		}

		/**
		 * Set default method.
		 *
		 * If there is no method specified in the url, then the specified method will be called.
		 */
		protected function setDefaultMethod($value) {
			$this->defaultMethod=$value;
		}

		/**
		 * Set reference to dispatcher.
		 *
		 * The dispatcher does this as part of creating the controller instance.
		 * This method should not be called from the user application.
		 */
		public function setDispatcher($value) {
			$this->dispatcher=$value;
		}

		/**
		 * Add method.
		 */
		private function addMethod($method) {
			if (!$this->methods)
				$this->methods=array();

			$method->setController($this);
			$this->methods[$method->getName()]=$method;
		}

		/**
		 * Set default type.
		 *
		 * Set the default result processing used by the methods of this controller.
		 * See {@link ControllerMethod::type} for details.
		 */
		protected function setDefaultType($type) {
			$this->defaultType=$type;
		}

		/**
		 * Get method for invocation.
		 *
		 * There is generally no need for user applications to use this method.
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
		 *
		 * The returned object is an instance of the {@link ControllerMethod}, so this 
		 * method is intended to be used something like:
		 *
		 * <code>
		 *     $this->method("getuserdata")->paths(1)->args("domain","group")->any()->type("json");
		 * </code>
		 *
		 * @param string $methodName Then name of the method.
		 * @see ControllerMethod
		 */
		protected function method($methodName) {
			$m=new ControllerMethod($methodName);
			$m->type($this->defaultType);
			$this->addMethod($m);

			return $m;
		}

		/**
		 * Fail.
		 *
		 * Calls fail in our {@link WebDispatcher}.
		 */
		public function fail($e) {
			$this->dispatcher->fail($e);
		}
	}