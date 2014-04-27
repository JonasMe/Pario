<?php
	/**
	 * Number Pario type.
	 * @author   Jonas@mersholm.dk
	 */

	namespace Jonm\Pario;

	class Number extends ParioType {

		/**
		 * Using laravel schema double databasetype.
		 * @var string
		 */
		protected $databasType = "double";

		/**
		 * Recommended html field type
		 * @var string
		 */
		protected $recommendedField = "text";

		/**
		 * Verifies that the output is a number, before outputting it.
		 * @param  double $number Input number from database
		 * @return double         outputting number
		 */
		public function renderOutput($number) {
			return ( is_numeric($string) ? $number : 0);
		}

		/**
		 * Verifies that the input is a number, before submitting it to the database
		 * @param  double $number
		 * @return double         
		 */
		public function renderInsert($number) {

			return ( is_numeric($number) ? str_replace( ",", ".", $number) : 0);
		}

	}