<?php

	/**
	 * Error passed to the registered error handler.
	 */
	class WebException extends Exception {

		/**
		 * Constructor.
		 */
		function __construct($message, $code=500, $previous=NULL) {
			parent::__construct($message, $code, $previous);
		}
	}