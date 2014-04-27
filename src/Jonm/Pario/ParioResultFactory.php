<?php
	namespace Jonm\Pario;

	class ParioResultFactory {

		protected $types;

		public function __construct($types) {
			$this->types = $types;
		}

		public function singleColumn($queryResult) {
			
			$result = new ParioResult();

			foreach( $queryResult as $key => $value ) {
				if( isset( $this->types[$key] ) ) {
					$result->{$key} = $this->types[$key]->renderOutput( $value );
				} else {
					$result->{$key} = $value;
				} 
			}

			return $result;
		}

		public function multipleColumns( $queryResult ) {
			$returner = array();
			foreach( $queryResult as $k => $v ) {
				$result = new ParioResult();
				foreach( $v as $key => $value ) {
					if( isset( $this->types[$key] ) ) {
						$result->{$key} = $this->types[$key]->renderOutput( $value );
					} else {
						$result->{$key} = $value;
					} 
				}
				$returner[] = $result;
			}

			return $returner;

		}


	}