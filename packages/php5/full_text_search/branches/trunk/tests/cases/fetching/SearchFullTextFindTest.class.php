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
require_once(dirname(__FILE__) . '/../../../full_text_search.class.php');
require_once(dirname(__FILE__) . '/../../../search_query.class.php');

class search_full_text_find_test extends LimbTestCase
{
  var $search = null;
  var $search_query = null;

  function search_full_text_find_test($name = 'full text search find test case')
  {
    parent :: LimbTestCase($name);
  }

  function setUp()
  {
    load_testing_db_dump(dirname(__FILE__) . '/../../sql/full_text_search.sql');

    $this->search_query = new search_query();
    $this->search = new full_text_search();
  }

  function tearDown()
  {
    clear_testing_db_tables();
  }

  function test_simple_find()
  {
    $this->search_query->add('mysql');
    $this->search_query->add('root');

    $result = $this->search->find($this->search_query);

    $this->assertEqual(array_keys($result),
      array(24, 26)
    );
  }

  function test_simple_find_only_class()
  {
    $this->search_query->add('данных');

    $result = $this->search->find($this->search_query, 100);
    $this->assertEqual(array_keys($result),
      array(20)
    );
  }

  function test_simple_find_whith_restricted_classes()
  {
    $this->search_query->add('restrict');

    $result = $this->search->find($this->search_query, null, $restricted_classes = array(110));
    $this->assertEqual(sizeof($result),2);
    $this->assertEqual(array_keys($result), array( 27, 26));
  }

  function test_simple_find_whith_allowed_classes()
  {
    $this->search_query->add('restrict');

    $result = $this->search->find($this->search_query, null, $restricted_classes = array(), $allowed_classes = array(110, 120));
    $this->assertEqual(sizeof($result),3);
    $this->assertEqual(array_keys($result), array(27, 25, 24));
  }

  function test_simple_find_by_ids()
  {
    $this->search_query->add('mysql');
    $this->search_query->add('root');

    $result = $this->search->find_by_ids(array(24), $this->search_query);
    $this->assertEqual(array_keys($result),
      array(24)
    );
  }

}
?>