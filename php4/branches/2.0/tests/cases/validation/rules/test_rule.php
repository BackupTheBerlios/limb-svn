<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/


require_once(LIMB_DIR . 'core/lib/validators/validator.class.php');
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');

Mock::generate('error_list');

Mock::generatePartial(
    'validator',
    'validator_test_version',
    array('_get_error_list')); 
    
class test_rule extends UnitTestCase
{
	var $validator = null;
	var $error_list = null;
	
	function setUp()
	{
   $this->error_list =& new Mockerror_list($this);
   $this->validator =& new validator_test_version($this);
   $this->validator->setReturnReference('_get_error_list', $this->error_list);
	} 
	
	function tearDown()
	{
		$this->error_list->tally();
		$this->validator->tally();
		unset($this->validator);
		unset($this->error_list);
	} 
} 

?>