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
require_once(dirname(__FILE__) . '/../../..//SearchQuery.class.php');

class SearchQueryTest extends LimbTestCase
{
  var $query_object = null;

  function searchQueryTest($name = 'search query test case')
  {
    parent :: LimbTestCase($name);
  }

  function setUp()
  {
    $this->query_object = new SearchQuery();
  }

  function testIsEmpty()
  {
    $this->assertTrue($this->query_object->isEmpty());
  }

  function testAdd()
  {
    $this->query_object->add('wow');
    $this->query_object->add('yo');

    $this->assertEqual($this->query_object->toString(), 'wow yo');
  }
}
?>