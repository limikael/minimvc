<?php

	/**
	 * Util for use with apache mod rewrite.
	 */
	class RewriteUtil {

		/**
		 * Get component of the path that comes after the location
		 * of the script.
		 */
		public static function getBase() {
			$pathinfo=pathinfo($_SERVER["SCRIPT_NAME"]);
			$dirname=$pathinfo["dirname"];
			$url=$_SERVER["REQUEST_URI"];

			//Logger::debug("url: ".$url);

			if (strpos($url,"?")!==FALSE)
				$url=substr($url,0,strpos($url,"?"));

			//Logger::debug("url: ".$url);

			if (substr($url,0,strlen($dirname))!=$dirname)
				throw new Exception("Somthing is malformed.");

			$s=substr($url,0,strlen($dirname))."/";

			return str_replace("//","/",$s);
		}

		/**
		 * Get base including host and protocol.
		 */
		public static function getBaseUrl() {
			$proto="http://";

			if (isset($_SERVER['HTTPS']))
				$proto="https://";

			return $proto.$_SERVER["SERVER_NAME"].self::getBase();
		}

		/**
		 * Get component of the path that comes after the location
		 * of the script.
		 */
		public static function getPath() {
			$pathinfo=pathinfo($_SERVER["SCRIPT_NAME"]);
			$dirname=$pathinfo["dirname"];
			$url=$_SERVER["REQUEST_URI"];

			//Logger::debug("url: ".$url);

			if (strpos($url,"?")!==FALSE)
				$url=substr($url,0,strpos($url,"?"));

			//Logger::debug("url: ".$url);

			if (substr($url,0,strlen($dirname))!=$dirname)
				throw new Exception("Somthing is malformed.");

			return substr($url,strlen($dirname));
		}

		/**
		 * Get path components.
		 */
		public static function getPathComponents() {
			return self::splitUrlPath(self::getPath());
		}

		/**
		 * Split url path.
		 */
		public static function splitUrlPath($path) {
			$components=explode("/",$path);

			while (sizeof($components)>0 && $components[sizeof($components)-1]==="")
				array_pop($components);

			while (sizeof($components)>0 && $components[0]==="")
				array_shift($components);

			return $components;
		}
	}