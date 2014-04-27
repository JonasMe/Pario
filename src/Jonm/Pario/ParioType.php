<?php
	namespace Jonm\Pario;

	abstract class ParioType implements Interfaces\TypeInterface {

		protected $name;
		protected $slug;
		protected $databasType;
		protected $renderOptions = array();

		public function __construct( $name = null ) {
			if( isset( $this->name ) && $this->name != "" && is_string($this->name) ) {
				$this->setName($this->name);
			} elseif ( !is_null($name) && is_string($name) ) {
				$this->setName($name);
			}
		}

		public function setName($name) {
			$this->name = $name;
			$this->slug = strtolower( preg_replace("/[^A-Za-z0-9]/", "", $this->name) );
		}

		public function getDbType() {
			return $this->databasType;
		}

		public function getSlug() {
			return $this->slug;
		}

		public function getName() {
			return $this->name;
		}


	}