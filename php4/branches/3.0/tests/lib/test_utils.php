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
require_once(LIMB_DIR . '/core/etc/limb_util.inc.php');

function make__FILE__readable($str)
{
  return str_replace('_', ' ', to_under_scores(basename($str, '.class.php')));
}

function registerTestingIni($ini_file, $content)
{
  $toolkit =& Limb :: toolkit();
  $toolkit->flushINIcache($ini_file);

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
      $toolkit =& Limb :: toolkit();
      $toolkit->flushINIcache($ini_file);
    }
  }

  $GLOBALS['testing_ini'] = array();

  clearstatcache();
}

function loadTestingDbDump($dump_path)
{
  if(!file_exists($dump_path))
    die('"' . $dump_path . '" sql dump file not found!');

  $tables = array();
  $sql_array = file($dump_path);

  $toolkit = Limb :: toolkit();
  $conn = $toolkit->getDbConnection();

  foreach($sql_array as $sql)
  {
    if(!preg_match("|insert\s+?into\s+?([^\s]+)|i", $sql, $matches))
      continue;

    if(isset($tables[$matches[1]]))
      continue;

    $tables[$matches[1]] = $matches[1];

    $stmt =& $conn->newStatement('DELETE FROM '. $matches[1]);
    $stmt->execute();
  }

  $GLOBALS['testing_db_tables'] = $tables;

  foreach($sql_array as $sql)
  {
    if(trim($sql))
    {
      $stmt =& $conn->newStatement($sql);
      $stmt->execute();
    }
  }
}

function clearTestingDbTables()
{
  if(!isset($GLOBALS['testing_db_tables']))
    return;

  $toolkit = Limb :: toolkit();
  $conn = $toolkit->getDbConnection();

  foreach($GLOBALS['testing_db_tables'] as $table)
  {
    $stmt =& $conn->newStatement('DELETE FROM '. $table);
    $stmt->execute();
  }

  $GLOBALS['testing_db_tables'] = array();
}

?>