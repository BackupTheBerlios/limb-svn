<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
@define('FAKEMAIL_PORT', 25);
@define('FAKEMAIL_HOST', 'localhost');
@define('FAKEMAIL_PATH', '.');
@define('FAKEMAIL_SCRIPT', './fakemail');

class FakemailDaemon
{
  var $pid = null;
  var $path = null;
  var $port = null;
  var $host = null;
  var $log  = null;

  function FakemailDaemon($path = null, $port = null, $host = null)
  {
    $this->path = is_null($path) ?  FAKEMAIL_PATH : $path;
    $this->port = is_null($port) ?  FAKEMAIL_PORT : $port;
    $this->host = is_null($host) ?  FAKEMAIL_HOST : $host;
  }

  function useLog($log = null)
  {
    $this->log = is_null($log) ?  $this->path . '/fakemail.log' : $log;
  }

  function start()
  {
    if(!file_exists($this->path) || !is_dir($this->path))
      die('Directory for fakemail \"'. $this->path .'\" not found' );

    $fakemail = "perl ". FAKEMAIL_SCRIPT ." --background --path={$this->path} --port={$this->port} --host={$this->host}" ;
    if($this->log)
      $fakemail .= " --log={$this->log}";
    $this->pid = exec($fakemail);
  }

  function stop()
  {
    if($this->pid)
      exec("kill {$this->pid}");
  }

  function clearLog()
  {
     unlink($this->log);
  }

  function removeRecipientMail($recipient)
  {
     $names = $this->_getRecipientFileNames($recipient);
     foreach($names as $name)
       $contents[] = unlink($this->path .'/'. $name);
  }

  function getRecipientMailCount($recipient)
  {
    return count($this->_getRecipientFileNames($recipient));
  }

  function getRecipientMailContents($recipient)
  {
     $contents = array();
     $names = $this->_getRecipientFileNames($recipient);
     foreach($names as $name)
       $contents[] = file_get_contents($this->path .'/'. $name);
     return $contents;
  }

  function _getRecipientFileNames($recipient)
  {
    $this_path = getcwd();
    $recipient_files = array();

    if (is_dir($this->path))
    {
      chdir($this->path);
      $handle = opendir('.');
      while (($file = readdir($handle)) !== false)
      {
        if ($file == "." || $file == ".." || $file == '.svn' || is_dir($file))
          continue;

        if (is_file($file) && strpos($file, $recipient .'.') !== false)
          $recipient_files[] = $file;
      }
      closedir($handle);
    }

    chdir($this_path);
    return $recipient_files;
  }
}
?>