<?php

	/**
	 * System util.
	 */
	class SystemUtil {

		/**
		 * Error handler.
		 */
		public static function exception_error_handler($errno, $errstr, $errfile, $errline) {
			Log::error($errstr);
//			Log::error("^ ".$errfile.":".$errline);
			throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
		}

		/**
		 * Throw exceptions for errors.
		 */
		public static function enableErrorExceptions() {
			set_error_handler("SystemUtil::exception_error_handler");
		}

		/**
		 * Disable magic quotes.
		 */
		public static function disableMagicQuotes() {
			if (get_magic_quotes_gpc()) {
				$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
				while (list($key, $val) = each($process)) {
					foreach ($val as $k => $v) {
						unset($process[$key][$k]);
						if (is_array($v)) {
							$process[$key][stripslashes($k)] = $v;
							$process[] = &$process[$key][stripslashes($k)];
 						} 

            			else {
			                $process[$key][stripslashes($k)] = stripslashes($v);
            			}
			        }
			    }
			    unset($process);
			}
		}
	}