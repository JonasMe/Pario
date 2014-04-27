<?php

	use \Jonm\Pario\Pario;
	use \Jonm\Pario\ParioGroup;
	use \Jonm\Pario\String;

	class Test extends TestCase {

		protected $testGroup;

	    public function doTest()
	    {
	        $this->assertTrue(true);
	    }

	    protected function addGroup() {
	    	$this->testGroup = new ParioGroup("test");
	    }

	    protected addType() {
	    	$type = new String();
	    	$type->setName("TestType");

	    	$this->testGroup->addType( $type );
	    }


	}