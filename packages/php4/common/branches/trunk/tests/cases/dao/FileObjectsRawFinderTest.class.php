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
require_once(dirname(__FILE__) . '/../../../finders/FileObjectsRawFinder.class.php');

Mock :: generatePartial('FileObjectsRawFinder',
                        'FileObjectsRawFinderTestVersion',
                        array('_doParentFind'));

class FileObjectsRawFinderTest extends LimbTestCase
{
  var $finder;

  function setUp()
  {
    $this->finder = new FileObjectsRawFinderTestVersion($this);
  }

  function tearDown()
  {
    $this->finder->tally();
  }

  function testFindEmpty()
  {
    $this->finder->setReturnValue('_doParentFind', array(), array(array(), array()));

    $this->assertTrue(!$this->finder->find());
  }

  function testFind()//!!! refactor, there should be real selects not just mocked ones
  {
    $params = array();
    $sql_params = array();
    $result = 'some result';

    $expected_sql_params = array();
    $expected_sql_params['columns'][] = ' m.file_name as file_name, m.mime_type as mime_type, m.etag as etag, m.size as size, ';
    $expected_sql_params['tables'][] = ', media as m ';
    $expected_sql_params['conditions'][] = ' AND tn.media_id=m.id ';

    $this->finder->expectOnce('_doParentFind', array($params, $expected_sql_params));
    $this->finder->setReturnValue('_doParentFind', $result, array($params, $expected_sql_params));

    $this->assertEqual($this->finder->find($params, $sql_params), $result);
  }
}

?>