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
require_once(dirname(__FILE__) . '/../../../normalizers/SearchTextNormalizer.class.php');

class SearchTextNormalizerTest extends LimbTestCase
{
  var $normalizer = null;

  function searchTextNormalizerTest($name = 'text search normalizer test case')
  {
    $this->normalizer = new SearchTextNormalizer();

    parent :: limbTestCase($name);
  }

  function testProcess()
  {
    $result = $this->normalizer->process('"mysql"
      wow-it\'s JUST \'so\' `cool` i can\'t believe it <b>root</b>"he-he"');

    $this->assertEqual($result, "mysql wow it's just so cool i can't believe it root he he");
  }
}
?>