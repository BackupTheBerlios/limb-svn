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
require_once(dirname(__FILE__) . '/../../../FullTextSearch.class.php');
require_once(dirname(__FILE__) . '/../../../SearchQuery.class.php');

class SearchFullTextFindTest extends LimbTestCase
{
  var $search = null;
  var $search_query = null;

  function searchFullTextFindTest($name = 'full text search find test case')
  {
    parent :: limbTestCase($name);
  }

  function setUp()
  {
    loadTestingDbDump(dirname(__FILE__) . '/../../sql/full_text_search.sql');

    $this->search_query = new SearchQuery();
    $this->search = new FullTextSearch();
  }

  function tearDown()
  {
    clearTestingDbTables();
  }

  function testSimpleFind()
  {
    $this->search_query->add('mysql');
    $this->search_query->add('root');

    $result = $this->search->find($this->search_query);

    $this->assertEqual(array_keys($result),
      array(24, 26)
    );
  }

  function testSimpleFindOnlyClass()
  {
    $this->search_query->add('данных');

    $result = $this->search->find($this->search_query, 100);
    $this->assertEqual(array_keys($result),
      array(20)
    );
  }

  function testSimpleFindWhithRestrictedClasses()
  {
    $this->search_query->add('restrict');

    $result = $this->search->find($this->search_query, null, $restricted_classes = array(110));
    $this->assertEqual(sizeof($result),2);
    $this->assertEqual(array_keys($result), array( 27, 26));
  }

  function testSimpleFindWhithAllowedClasses()
  {
    $this->search_query->add('restrict');

    $result = $this->search->find($this->search_query, null, $restricted_classes = array(), $allowed_classes = array(110, 120));
    $this->assertEqual(sizeof($result),3);
    $this->assertEqual(array_keys($result), array(27, 25, 24));
  }

  function testSimpleFindByIds()
  {
    $this->search_query->add('mysql');
    $this->search_query->add('root');

    $result = $this->search->findByIds(array(24), $this->search_query);
    $this->assertEqual(array_keys($result),
      array(24)
    );
  }

}
?>