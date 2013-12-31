<?php

	require_once "dispatcher/ControllerMethod.php";

	/**
	 * Base application controller.
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
		public function ControllerMethod($name, $numPathArgs, $requestParameters) {
			$this->methodName=$name;
			$this->requestParameters=$requestParameters;
			$this->numPathArgs=$numPathArgs;
		}

		/**
		 * Set reference to controller.
		 */
		public function setController($value) {
			$this->controller=$value;
		}

		/**
		 * Set result processing.
		 */
		public function setResultProcessing($value) {
			$this->resultProcessing=$value;
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
					Log::error("Error in json call: ".$message."\n".$trace);
					$r=array("ok"=>0,"message"=>$message);
					echo json_encode($r);
					break;

				default:
					echo "fail..";
					echo $message;
					echo "<pre>".$trace."</pre>";
					break;
			}

			exit();
		}
	}