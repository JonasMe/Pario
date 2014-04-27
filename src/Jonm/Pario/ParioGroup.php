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

		/**
		 * Path to the definition cache file
		 * @var string
		 */
		protected $definitionFile;

		/**
		 * Boolean to check file existance
		 * @var boolean
		 */
		protected $hasFile;

		/**
		 * Holder for the ParioResultFactory
		 * @var ParioResultFactory
		 */
		protected $factory;

		/**
		 * Defining simple constants
		 */
		const REMOVE 	= true;
		const KEEP 		= false;

		/**
		 * Constructor method to initialize member variables
		 * @param string $parioName Name of the group
		 */
		public function __construct( $parioName ) {
			$this->types = array();
			$this->name = $parioName;
			$this->slug = $this->namify( $this->name );
			$this->definitionFile = storage_path() . "/pario/" . "ParioGroupDescription_" . $this->slug . ".json";
			$this->hasFile = ( file_exists( $this->definitionFile ) ? true : false );
			$this->getDefinition();
			$this->factory = new ParioResultFactory($this->types);
		}


		/**
		 * Public method to save the schema and definition
		 * @return ParioGroup Returns the groups current state
		 */
		public function save() {
			$this->saveSchema();
			$this->saveDefinition();
			return $this;
		}

		/**
		 * Public method to delete the current groups definition and schema
		 * @return none
		 */
		public function delete() {
			\Schema::dropIfExists('pario_' . $this->slug);
			if( $this->hasFile ) { unlink($this->definitionFile); }
		}

		/**
		 * Adds a key => value array to the groups schema
		 * @param array $array Array containing values to add to the database
		 */
		public function add($array) {
			$insArr = array();
			foreach( $array as $k => $v) {
				if( isset( $this->definition->types[$k] ) ) { $insArr[$k] = $this->definition->types[$k]->renderInsert($v); }
			}

			$id = \DB::table('pario_' . $this->slug)->insertGetId( $insArr );
			return $id;
		}

		/**
		 * Finds a result by id
		 * @param  integer $id
		 * @return ParioResult
		 */
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

		/**
		 * Retrieves all rows
		 * @return array Array of ParioResult instances
		 */
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

		/**
		 * Removes a type from the definition and scheme
		 * @param  typename $type
		 * @return none
		 */
		public function removeType($type) {
			$type = $this->namify( $type );
			if( isset( $this->definition->types[$type] ) ) {
				$this->definition->types[$type]->do = self::REMOVE;
			} elseif( isset( $this->types[$type] ) ) {
				$this->types[$type]->do = self::REMOVE;
			}

		}

		/**
		 * Adds a type to the definition. Waiting for save to persist to database
		 * @param ParioType $type
		 * @param ParioGroup    $name Returns own state.
		 */
		public function addType( ParioType $type, $name = null ) {
			$type->do = self::KEEP;
			
			if( !is_null($name) && is_string($name) ) {
				$type->setName( $name );
			}

			$this->types[ $type->getSlug() ] = $type;
			return $this;
		}

		/**
		 * Saves the schema updated with new types or removing types
		 * @return none
		 */
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


		/**
		 * Saving the definition file
		 * @return none
		 */
		protected function saveDefinition() {
			
			$definition = (object) [];

			foreach( $this->types as $slug => $type ) {
				$definition->types[ $slug ] = $type;
			}
			
			file_put_contents($this->definitionFile, serialize( $definition ));
			$this->getDefinition();

		}

		/**
		 * Helperfunction to make name slugs
		 * @param  string $string 
		 * @return none         
		 */
		protected function namify($string) {
			return strtolower( preg_replace("/[^A-Za-z0-9]/", "", $string) );
		}

		/**
		 * Retrives the definition from file
		 * @return none
		 */
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