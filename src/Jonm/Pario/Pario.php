<?php
	namespace Jonm\Pario;
	use \Jonm\Pario\Type;

	class Pario {

		/**
		 * Holder for namespaces, where types may be found
		 * @var array
		 */
		protected $typeSpaces = [];

		/**
		 * Create a new group
		 * @param  string $groupName
		 * @param  method $function  
		 * @return ParioGroup
		 */
		public function create($groupName, $function) {
			$group = new ParioGroup( $groupName );
			$function($group);
			return $group->save();

		}

		/**
		 * Retrieves a group by name
		 * @param  string $groupName
		 * @return ParioGroup          
		 */
		public function make($groupName) {
			return new ParioGroup( $groupName );
		}

		/**
		 * Deletes a group by name
		 * @param  string $groupName 
		 * @return none            
		 */
		public function delete($groupName) {
			$group = new ParioGroup($groupName);
			$group->delete();
			unset($group);
		}

		/**
		 * Adds a namespace to the type search
		 * @param string $space No leading slashes
		 */
		public function addTypeSpace($space) {
			if( !in_array($space, $this->typeSpaces) ) {
				$this->typeSpaces[] = $space;
			}
		}

		/**
		 * Retrieves a Pario type, and sets it name if wished.
		 * @param  string $type Type
		 * @param  string $name Set the name of the field
		 * @return ParioType
		 */		
		public function type( $type, $name = null ) {
			
			$type = ucfirst($type);

			foreach( $this->typeSpaces as $space ) {
				$class = "\\".$space . "\\" .$type;
				if( class_exists( $class ) ) {
					$class = new $class;
					if( !is_null($name) ) { $class->setName($name); }
					return $class;
				}
			}

			$default = __NAMESPACE__ . "\\" . $type;
			if( class_exists($default) ) {
					$class = new $default;
					if( !is_null($name) ) { $class->setName($name); }
					return $class;
			} 

			throw new \Exception("Type was not found in spaces : " . implode(",", $this->typeSpaces) . ", nor in default.");
		}

	}