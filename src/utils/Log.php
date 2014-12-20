<?php
	/**
	 * Logging.
	 * This should really not be here....
	 * Not this either
	 */
	class Log {

		private static $fn;
		private static $useConsole;
		private static $level;

		/**
		 * Init file logging.
		 */
		public static function initFile($fn, $level="debug") {
			self::$fn=$fn;
			self::$useConsole=false;

//			self::$file=fopen($fn,"a");
			self::setLevel($level);
		}

		/**
		 * Init console logging.
		 */
		public static function initConsole($level="debug") {
			self::$fn=NULL;
			self::$useConsole=true;

//			self::$file=fopen('php://stdout','w');
			self::setLevel($level);
		}

		/**
		 * Set level.
		 */
		private static function setLevel($level) {
			switch ($level) {
				case "debug":
					self::$level=3;
					break;

				case "info":
					self::$level=2;
					break;

				case "warning":
					self::$level=1;
					break;

				case "error":
					self::$level=0;
					break;
			}
		}

		/**
		 * Message.
		 */
		private static function message($channel, $level, $message) {
			if ((self::$fn || self::$useConsole) && $level<=self::$level) {
				if (self::$useConsole)
					$file=fopen("php://stdout","w");

				else
					$file=fopen(self::$fn,"a");

				fwrite($file,date("M j H:i:s")." ".$channel.": ".$message."\n");
				fflush($file);
				fclose($file);
			}
		}

		/**
		 * Send a debug message to the log.
		 */
		public static function debug($message) {
			self::message("debug",3,$message);
		}

		/**
		 * Send an info message to the log.
		 */
		public static function info($message) {
			self::message("info",2,$message);
		}

		/**
		 * Send a warning message to the log.
		 */
		public static function warning($message) {
			self::message("warning",1,$message);
		}

		/**
		 * Send an error message to the log.
		 */
		public static function error($message) {
			self::message("error",0,$message);
		}
	}
