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

SimpleTestOptions::ignore('db_test');

//ugly!!! db_test class should be removed
class db_test extends LimbTestCase
{
  var $db = null;

  var $dump_file = '';

  var $tables_list = array();
  var $table_records = array();

  var $sql_array = array();

  function db_test()
  {
    $this->db =& db_factory :: instance();

    parent :: LimbTestCase();
  }

  function _clean_up()
  {
    foreach($this->tables_list as $table)
      $this->db->sql_delete($table);
  }

  function _get_dump_file_path()
  {
    $file = PROJECT_DIR . '/tests/sql/' . $this->dump_file;

    if(!file_exists($file))
      $file = LIMB_DIR . '/tests/sql/' . $this->dump_file;

    return $file;
  }

  function _load_tables_list()
  {
    if(!$this->dump_file)
      return;

    $this->sql_array = file($this->_get_dump_file_path());

    $tables = array();

    foreach($this->sql_array as $sql)
    {
      if(preg_match("|insert\s+?into\s+?([^\s]+)|i", $sql, $matches))
      {
        if(!isset($tables[$matches[1]]))
        {
          $this->tables_list[] = $matches[1];

          if(!isset($this->table_records[$matches[1]]))
            $this->table_records[$matches[1]] = 0;

          $this->table_records[$matches[1]]++;

        }
      }
    }
  }

  function _load_dumped_db()
  {
    if(!$this->dump_file)
      return;

    if(!file_exists($file = $this->_get_dump_file_path()))
      die('"' . $file . '" sql dump file not found!');

    $this->sql_array = file($file);

    foreach($this->sql_array as $sql)
    {
      $this->db->sql_exec($sql);
    }
  }

  function setUp()
  {
    debug_mock :: init($this);

    $this->_load_tables_list();

    $this->_clean_up();

    $this->_load_dumped_db();
  }

  function tearDown()
  {
    debug_mock :: tally();

    $this->_clean_up();
  }

}
?>