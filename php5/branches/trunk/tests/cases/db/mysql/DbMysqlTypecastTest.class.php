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
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbMysql.class.php');

class DbMysqlTypecastTest extends LimbTestCase
{
  var $db = null;

  function dbMysqlTypecastTest($name = 'mysql typecast db test case')
  {
    parent :: limbTestCase($name);
  }

  function setUp()
  {
    $this->db =& DbFactory :: instance();
    $this->db->setLocaleId('en');
  }

  function testDefaultTypes()
  {
    $this->assertEqual(
      array(
        'id' => "'1'",
        'title' => "' \\\"\\\" title\''",
        'null' => 'NULL',
        'bool_true' => 1,
        'bool_false' => 0
      ),
      $this->db->processValues(
        array(
          'id' => 1,
          'title' => " \"\" title'",
          'null' => null,
          'bool_true' => true,
          'bool_false' => false,
        )
      )
    );
  }

  function testDefinedTypes()
  {
    $this->assertEqual(
      array(
        'id' => 0,
        'id1' => 1000,
        'date_not_iso' => '\'1982-12-01\'',
        'date_iso' => '\'1982-12-01\'',
        'datetime_not_iso' => '\'1982-12-01 12:01:59\'',
        'datetime_iso' => '\'1982-12-01 12:01:59\'',
        'title' => "' \\\"\\\" title\''",
      ),
      $this->db->processValues(
        array(
          'id' => 'abc zxc',
          'id1' => '1000',
          'date_not_iso' => '12/01/1982',
          'date_iso' => '1982-12-01',
          'datetime_not_iso' => '12/01/1982 12:01:59',
          'datetime_iso' => '1982-12-01 12:01:59',
          'title' => " \"\" title'",
        ),
        array(
          'id' => 'numeric',
          'id1' => 'numeric',
          'date_not_iso' => 'date',
          'date_iso' => 'date',
          'datetime_not_iso' => 'datetime',
          'datetime_iso' => 'datetime',
          'title' => 'string'
        )
      )
    );
  }

  function testInsertDefinedWithNotDefinedTypes()
  {
    $this->assertEqual(
      array(
        'null' => 'NULL',
        'bool_true' => 1,
        'bool_false' => 0,
        'id' => "'abc zxc'",
        'id1' => 1000,
        'date_not_iso' => '\'1982-12-01\'',
        'date_iso' => '\'1982-12-01\'',
        'datetime_not_iso' => '\'1982-12-01 12:01:59\'',
        'datetime_iso' => '\'1982-12-01 12:01:59\'',
        'title' => "' \\\"\\\" title\''",
      ),
      $this->db->processValues(
        array(
          'null' => null,
          'bool_true' => true,
          'bool_false' => false,
          'id' => 'abc zxc',
          'id1' => '1000',
          'date_not_iso' => '12/01/1982',
          'date_iso' => '1982-12-01',
          'datetime_not_iso' => '12/01/1982 12:01:59',
          'datetime_iso' => '1982-12-01 12:01:59',
          'title' => " \"\" title'",
        ),
        array(
          'id1' => 'numeric',
          'date_not_iso' => 'date',
          'date_iso' => 'date',
          'datetime_not_iso' => 'datetime',
          'datetime_iso' => 'datetime',
          'title' => 'string'
        )
      )
    );
  }

}
?>