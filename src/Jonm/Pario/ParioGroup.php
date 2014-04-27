<?php
	namespace Jonm\Pario;

	class ParioGroup {

		/**
		 * Group name
		 * @var string
		 */
		protected $name;

		/**
		 * Slug
		 */
		protected $slug;

		/**
		 * Existing definition
		 * @var definition
		 */
		private $definition;

		/**
		 * New additions to definition
		 * @var types
		 */
		private $types;

		protected $definitionFile;

		protected $hasFile;

		protected $factory;

		const REMOVE 	= true;
		const KEEP 		= false;

		public function __construct( $parioName ) {
			$this->types = array();
			$this->name = $parioName;
			$this->slug = $this->namify( $this->name );
			$this->definitionFile = storage_path() . "/pario/" . "ParioGroupDescription_" . $this->slug . ".json";
			$this->hasFile = ( file_exists( $this->definitionFile ) ? true : false );
			$this->getDefinition();
			$this->factory = new ParioResultFactory($this->types);
		}

		public function save() {
			$this->saveSchema();
			$this->saveDefinition();
			return $this;
		}

		public function delete() {
			\Schema::dropIfExists('pario_' . $this->slug);
			if( $this->hasFile ) { unlink($this->definitionFile); }
		}

		public function add($array) {
			$insArr = array();
			foreach( $array as $k => $v) {
				if( isset( $this->definition->types[$k] ) ) { $insArr[$k] = $this->definition->types[$k]->renderInsert($v); }
			}

			$id = \DB::table('pario_' . $this->slug)->insertGetId( $insArr );
			return $id;
		}

		public function find($id) {
			if( isset( $this->definition ) && isset( $this->slug) ) {
				$data = \DB::table('pario_' . $this->slug)->where($this->slug . "_id", '=', $id)->first();
				if( $data ) {
					$result = $this->factory->singleColumn( $data );
					return $result;
				} else {
					return "Bullshit!";
				}
			}
		}

		public function all() {
			$data = \DB::table('pario_' . $this->slug)->get();
			if( $data ) {
				return $this->factory->multipleColumns( $data );
			} else {
				return "NO!";
			}
		}

		public function queryBuild() {
			return \DB::table('pario_' . $this->slug);
		}

		public function get( $query ) {
			$data = $query->get();
			if( $data ) {
				return $this->factory->multipleColumns( $data );
			} else {
				return "no!";
			}
		}

		public function removeType($type) {
			$type = $this->namify( $type );
			if( isset( $this->definition->types[$type] ) ) {
				$this->definition->types[$type]->do = self::REMOVE;
			} elseif( isset( $this->types[$type] ) ) {
				$this->types[$type]->do = self::REMOVE;
			}

		}

		public function addType( ParioType $type ) {
			$type->do = self::KEEP;
			$this->types[ $type->getSlug() ] = $type;
			return $this;
		}

		protected function saveSchema() {
			$tableName = 'pario_' . $this->slug;

			if( \Schema::hasTable($tableName) ) {

				\Schema::table( $tableName , function($table) use($tableName)
				{
					
					foreach( $this->types as $t ) {
						if( !\Schema::hasColumn( $tableName, $t->getSlug() ) ) {
						if( $t->do == self::KEEP ) {
								$table->{$t->getDbType()}($t->getSlug());
						} elseif($t->do == self::REMOVE ) {
							   $table->dropColumn( $t->getSlug() );
						}
					}
				}
				});
			} else {
				\Schema::create( $tableName , function($table)
				{
					$table->increments( $this->slug . "_id" );
					foreach( $this->types as $t ) {
						if( $t->do == self::KEEP ) {
							$table->{$t->getDbType()}($t->getSlug());
						}
					}

				});
			}

		}



		protected function saveDefinition() {
			
			$definition = (object) [];

			foreach( $this->types as $slug => $type ) {
				$definition->types[ $slug ] = $type;
			}
			
			file_put_contents($this->definitionFile, serialize( $definition ));
			$this->getDefinition();

		}

		protected function namify($string) {
			return strtolower( preg_replace("/[^A-Za-z0-9]/", "", $string) );
		}

		private function getDefinition() {
			if( $this->hasFile ) {
				$definition = unserialize( file_get_contents($this->definitionFile) );
				$this->definition = $definition;
				foreach( $this->definition->types as $slug => $type ) {
					$this->types[$slug] = $type;
				}
			} else {

			}

		}
	}