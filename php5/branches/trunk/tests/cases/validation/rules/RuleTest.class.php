<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/validators/Validator.class.php');
require_once(LIMB_DIR . '/class/core/Dataspace.class.php');

Mock :: generate('ErrorList');

Mock :: generatePartial(
    'Validator',
    'ValidatorTestVersion',
    array('_getErrorList'));

SimpleTestOptions :: ignore('RuleTest');

class RuleTest extends LimbTestCase
{
  var $validator = null;
  var $error_list = null;

  function setUp()
  {
   $this->error_list = new MockErrorList($this);
   $this->validator = new ValidatorTestVersion($this);
   $this->validator->setReturnValue('_getErrorList', $this->error_list);
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