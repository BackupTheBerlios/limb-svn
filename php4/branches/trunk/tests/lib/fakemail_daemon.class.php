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

class FakemailDaemon
{
  var $pid = null;
  var $path = null;
  var $port = null;
  var $path = null;
  var $log  = null;

  function start()
  {
    $this->path = FAKEMAIL_PATH;
    $this->port = FAKEMAIL_PORT;
    $this->host = FAKEMAIL_HOST;
    $this->log  = FAKEMAIL_PATH . '/fakemail.log';

    $fakemail = "perl ". FAKEMAIL_SCRIPT ." --background --path={$this->path} --port={$this->port} --host={$this->host} --log={$this->log}" ;
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
     $names = $this->getRecipientFileNames($recipient);
     foreach($names as $name)
       $contents[] = unlink($this->path .'/'. $name);
  }

  function getRecipientMailCount($recipient)
  {
    return count($this->getRecipientFileNames($recipient));
  }

  function getRecipientMailContents($recipient)
  {
     $contents = array();
     $names = $this->getRecipientFileNames($recipient);
     foreach($names as $name)
       $contents[] = file_get_contents($this->path .'/'. $name);
     return $contents;
  }

  function getRecipientFileNames($recipient)
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