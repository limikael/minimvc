<?php

	/**
	 * Echo for attribute.
	 */
	function echo_attr($s) {
		echo htmlspecialchars($s);
	}

	/**
	 * Echo for html.
	 */
	function echo_html($s) {
		echo htmlspecialchars($s);
	}

	/**
	 * Template.
	 */
	class Template {

		private $filename;
		private $vars;

		/**
		 * Takes search path as argument.
		 */
		public function Template($filename) {
			$this->filename=$filename;
			$this->vars=array();
		}

		/**
		 * Set a variable.
		 */
		public function set($name, $value) {
			$this->vars[$name]=$value;
		}

		/**
		 * Show template.
		 */
		public function show() {
			foreach ($this->vars as $key=>$value)
				$$key=$value;

			require $this->filename;
		}

		/**
		 * Render template.
		 */
		public function render() {
			foreach ($this->vars as $key=>$value)
				$$key=$value;

			ob_start();
			require $this->filename;
			return ob_get_clean();
		}
	}