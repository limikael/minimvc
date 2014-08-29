<?php

	/**
	 * Method definition.
	 */
	class ControllerMethod {

		private $controller;
		private $methodName;
		private $requestParameters;
		private $resultProcessing;
		private $numPathArgs;

		/**
		 * Constructor.
		 */
		public function ControllerMethod($name) {
			$this->methodName=$name;
			$this->requestParameters=array();;
			$this->numPathArgs=0;
		}

		/**
		 * Set reference to controller.
		 */
		public function setController($value) {
			$this->controller=$value;
		}

		/**
		 * Set arguments.
		 */
		public function args(/*...*/) {
			$this->requestParameters=func_get_args();

			return $this;
		}

		/**
		 * Set arguments.
		 */
		public function paths($num) {
			$this->numPathArgs=$num;

			return $this;
		}

		/**
		 * Set result processing.
		 */
		public function type($value) {
			$this->resultProcessing=$value;

			return $this;
		}

		/**
		 * Get name.
		 */
		public function getName() {
			return $this->methodName;
		}

		/**
		 * Invoke.
		 */
		public function invoke($pathArgs, $requestArgs) {
			$invokeParams=$pathArgs;

			if (sizeof($pathArgs)!=$this->numPathArgs)
				$this->fail("Expected ".$this->numPathArgs." path arguments, got ".sizeof($pathArgs));

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
						$this->fail("Missing parameter $paramName");
				}
			}

			try {
				$result=call_user_func_array(array($this->controller,$this->methodName),$invokeParams);
			}

			catch (Exception $e) {
				$this->fail($e->getMessage(),$e->getTraceAsString());
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
		 * Fail.
		 */
		private function fail($message, $trace=NULL) {
			switch ($this->resultProcessing) {
				case "json":
					header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
					$r=array("ok"=>0,"message"=>$message,"trace"=>$trace);
					echo json_encode($r);
					break;

				default:
					$this->controller->fail($message,$trace);
					break;
			}

			exit();
		}
	}