<?php
	namespace Jonm\Pario;

	use Illuminate\Support\Facades\Facade;

	class ParioFacade extends Facade {

	    protected static function getFacadeAccessor() { return 'pario'; }

	}