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
require_once(LIMB_DIR . '/core/model/search/normalizers/search_text_normalizer.class.php');

class search_text_normalizer_test extends LimbTestCase
{
  var $normalizer = null;

  function search_text_normalizer_test($name = 'text search normalizer test case')
  {
    $this->normalizer = new search_text_normalizer();

    parent :: LimbTestCase($name);
  }

  function test_process()
  {
    $result = $this->normalizer->process('"mysql"
      wow-it\'s JUST \'so\' `cool` i can\'t believe it <b>root</b>"he-he"');

    $this->assertEqual($result, "mysql wow it's just so cool i can't believe it root he he");
  }
}
?>