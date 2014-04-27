<?php
	/**
	 * String Pario type.
	 * @author   Jonas@mersholm.dk
	 */

	namespace Jonm\Pario;
	
	class String extends ParioType {

		/**
		 * Using laravel schema String databasetype.
		 * @var string
		 */
		protected $databasType = "string";

		/**
		 * Verifies that the output is a string, before outputting it.
		 * @param  string $string Input string from database
		 * @return string         outputting string
		 */
		public function renderOutput($string) {
			return ( is_string($string) ? $string : "");
		}

		/**
		 * Verifies that the input is a string, before submitting it to the database
		 * @param  string $string
		 * @return string         
		 */
		public function renderInsert($string) {

			return ( is_string($string) ? $string : "");
		}

	}