<?php

	require_once __DIR__."/WebException.php";

	/**
	 * A method definition for a controller.
	 *
	 * This class is generally not instantiated directly, but rather using the
	 * {@link WebController::method} method from within the constructor of the controller, using
	 * syntax like this:
	 *
	 * <code>
	 *     $this->method("getuserdata")->paths(1)->args("domain","group")->any()->type("json");
	 * </code>
	 *
	 * @see WebController
	 */
	class ControllerMethod {

		private $controller;
		private $methodName;
		private $requestParameters;
		private $resultProcessing;
		private $numPathArgs;
		private $anyArgs;

		/**
		 * Constructor.
		 *
		 * Generally not instantiated directly.
		 */
		public function ControllerMethod($name) {
			$this->methodName=$name;
			$this->requestParameters=array();;
			$this->numPathArgs=0;
			$this->anyArgs=FALSE;
		}

		/**
		 * Set reference to controller.
		 *
		 * No need to use this from inside user applications.
		 */
		public function setController($value) {
			$this->controller=$value;
		}

		/**
		 * Set arguments that we expect as request parameters.
		 *
		 * Set arguments that this method should take as request parameters. For example, if we
		 * expect calls like this to our method:
		 *
		 * http://localhost/hello/world/?a=test&b=something
		 *
		 * Then we should declare it like this from within the constructor of the controller:
		 *
		 * <code>
		 *     $this->method("world")->args("a","b");
		 * </code>
		 *
		 * And then the actual method should be declared to take corresponding arguments:
		 *
		 * <code>
		 *     function world($a, $b) {
		 *         // ...
		 *     }
		 * </code>
		 */
		public function args(/*...*/) {
			$this->requestParameters=func_get_args();

			return $this;
		}

		/**
		 * Set number of arguments that we expect on the path.
		 *
		 * For example, if we have a controller called `user`, a method called `profile`
		 * and expect this to be invoked like this:
		 *
		 * http://localhost/user/profile/some_user/?expanded=true
		 *
		 * Then we can declare the method like this:
		 *
		 * <code>
		 *     $this->method("profile")->paths(1)->args("expanded");
		 * </code>
		 *
		 * And we would use the corresponding definition for our function:
		 *
		 * <code>
		 *     function profile($username, $expanded) {
		 *         // ...
		 *     }
		 * </code>
		 */
		public function paths($num) {
			$this->numPathArgs=$num;

			return $this;
		}

		/**
		 * Set result processing.
		 *
		 * Set the type of result processing that should be applied to the results of the method.
		 * By default, the methods are expected to output what the want to be sent to the browser.
		 * Using this method we can alter this behaivour and let the methods return something instead,
		 * which will then be processed and then outputted to the browser.
		 *
		 * The only supported type currently is `json`, in which case the method is supposed to
		 * return an array, which will be json encoded and sent to the browser.
		 *
		 * This method also alters the behaivour in the case where an exception is thrown from
		 * within the method. In the case of json, the error will be presented in a json
		 * parsable way also.
		 */
		public function type($value) {
			$this->resultProcessing=$value;

			return $this;
		}

		/**
		 * Specify that this method expects any parameters.
		 *
		 * Using this function will specify that the full $_REQUEST array will be passed to
		 * the method.
		 */
		public function any() {
			$this->anyArgs=TRUE;

			return $this;
		}

		/**
		 * Get name.
		 *
		 * For internal use.
		 */
		public function getName() {
			return $this->methodName;
		}

		/**
		 * Invoke.
		 *
		 * For internal use.
		 */
		public function invoke($pathArgs, $requestArgs) {
			$invokeParams=$pathArgs;

			if (sizeof($pathArgs)!=$this->numPathArgs)
				$this->fail(new WebException("Expected ".$this->numPathArgs." path arguments, got ".sizeof($pathArgs)));

			foreach ($this->requestParameters as $paramName) {
				$optional=FALSE;
				if (strstr($paramName,"=")!==FALSE)
					$optional=TRUE;

				$paramName=str_replace("=","",$paramName);

				if (array_key_exists($paramName,$requestArgs))
					$invokeParams[]=$requestArgs[$paramName];

				else {
					if ($optional)
						$invokeParams[]=NULL;

					else
						$this->fail(new WebException("Missing parameter $paramName"));
				}
			}

			if ($this->anyArgs)
				$invokeParams[]=$requestArgs;

			try {
				$result=call_user_func_array(array($this->controller,$this->methodName),$invokeParams);
			}

			catch (Exception $e) {
				$this->fail(new WebException($e->getMessage(),500,$e));
			}

			switch ($this->resultProcessing) {
				case "json":
					if (!is_array($result))
						$result=array();

					if (!array_key_exists("ok",$result))
						$result["ok"]=1;
	
					echo json_encode($result);
					return;
			}
		}

		/**
		 * Invoke raw.
		 */
		public function invokeRaw($params) {
			call_user_func_array(array($this->controller,$this->methodName),$params);
		}

		/**
		 * Fail.
		 * 
		 * Call fail in our {@link WebDispatcher}.
		 */
		private function fail($e) {
			switch ($this->resultProcessing) {
				case "json":
					$code=500;

					if ($e instanceof WebException)
						$code=$e->getCode();

					if (!headers_sent())
						header($_SERVER['SERVER_PROTOCOL'] . ' $code Internal Server Error', true, $code);

					$r=array("ok"=>0,"message"=>$e->getMessage(),"trace"=>$e->getTrageAsString());
					echo json_encode($r);
					break;

				default:
					$this->controller->fail($e);
					break;
			}

			exit();
		}
	}