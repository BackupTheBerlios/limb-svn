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
require_once(LIMB_DIR . '/class/search/normalizers/SearchPhoneNumberNormalizer.class.php');

class SearchPhoneNumberNormalizerTest extends LimbTestCase
{
  var $normalizer = null;

  function searchPhoneNumberNormalizerTest($name = 'phone number search normalizer test case')
  {
    $this->normalizer = new SearchPhoneNumberNormalizer();

    parent :: LimbTestCase($name);
  }

  function testProcess()
  {
    $result = $this->normalizer->process('тел.+7
      (8412)<b>5689-456-67</b>');

    $this->assertEqual($result, '78412568945667 8412568945667 568945667');
  }

  function testProcessOneNumber()
  {
    $result = $this->normalizer->process('234');

    $this->assertEqual($result, '234');
  }

  function testProcessEmpty()
  {
    $result = $this->normalizer->process('<b>nothing at all</b>');

    $this->assertEqual($result, '');
  }

}
?>