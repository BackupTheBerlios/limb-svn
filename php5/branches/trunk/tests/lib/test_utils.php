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

function registerTestingIni($ini_file, $content)
{
  if (isset($GLOBALS['testing_ini'][$ini_file]))
    die("Duplicate ini registration not allowed.");

  $GLOBALS['testing_ini'][$ini_file] = 1;

  $f = fopen(VAR_DIR . '/' . $ini_file, 'w');

  fwrite($f, $content, strlen($content));
  fclose($f);
}

function clearTestingIni()
{
  if(!isset($GLOBALS['testing_ini']) ||  !count($GLOBALS['testing_ini']))
    return;

  foreach(array_keys($GLOBALS['testing_ini']) as $ini_file)
  {
    if(file_exists(VAR_DIR . '/' . $ini_file))
    {
      unlink(VAR_DIR . '/' . $ini_file);
    }
  }

  $GLOBALS['testing_ini'] = array();

  clearstatcache();

  Limb :: toolkit()->flushINIcache();
}

function loadTestingDbDump($dump_path)
{
  if(!file_exists($dump_path))
    die('"' . $dump_path . '" sql dump file not found!');

  $tables = array();
  $sql_array = file($dump_path);

  $db = DbFactory::instance();

  foreach($sql_array as $sql)
  {
    if(!preg_match("|insert\s+?into\s+?([^\s]+)|i", $sql, $matches))
      continue;

    if(isset($tables[$matches[1]]))
      continue;

    $tables[$matches[1]] = $matches[1];
    $db->sqlDelete($matches[1]);
  }

  $GLOBALS['testing_db_tables'] = $tables;

  foreach($sql_array as $sql)
  {
    if(trim($sql))
      $db->sqlExec($sql);
  }
}

function clearTestingDbTables()
{
  if(!isset($GLOBALS['testing_db_tables']))
    return;

  $db = DbFactory::instance();

  foreach($GLOBALS['testing_db_tables'] as $table)
    $db->sqlDelete($table);

  $GLOBALS['testing_db_tables'] = array();
}

?>