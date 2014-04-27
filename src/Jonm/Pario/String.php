<?php
	namespace Jonm\Pario;

	class String extends ParioType {

		protected $databasType = "string";
		protected $renderOptions = array();

		public function renderOutput($string) {
			if( is_string($string) ) {
				return "Renderet ".$string;
			}
		}

		public function renderInsert($string) {
			return "insert " . $string;
		}

	}