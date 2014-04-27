<?php
	namespace Jonm\Pario;

	class Pario {

		public function create($groupName, $function) {
			$group = new ParioGroup( $groupName );
			$function($group);
			return $group->save();

		}

		public function make($groupName) {
			return new ParioGroup( $groupName );
		}

		public function delete($groupName) {
			$group = new ParioGroup($groupName);
			$group->delete();
			unset($group);
		}

	}