<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/core/lib/db/db_mysql.class.php');

class db_mysql_typecast_test extends LimbTestCase
{
  var $db = null;

  function db_mysql_typecast_test($name = 'mysql db test case')
  {
    parent :: LimbTestCase($name);
  }

  function setUp()
  {
    $this->db =& db_factory :: instance();
    $this->db->set_locale_id('en');
  }

  function test_default_types()
  {
    $this->assertEqual(
      array(
        'id' => "'1'",
        'title' => "' \\\"\\\" title\''",
        'null' => "''",
        'bool_true' => 1,
        'bool_false' => 0
      ),
      $this->db->_process_values(
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

  function test_defined_types()
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
      $this->db->_process_values(
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

  function test_insert_defined_with_not_defined_types()
  {
    $this->assertEqual(
      array(
        'null' => "''",
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
      $this->db->_process_values(
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