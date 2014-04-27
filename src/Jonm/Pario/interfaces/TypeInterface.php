<?php
	namespace Jonm\Pario\Interfaces;

	interface TypeInterface {
		public function getDbType();
		public function getName();
		public function getSlug();
		public function renderInsert( $string );
		public function renderOutput( $string );
	}