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
@define('FAKEMAIL_SCRIPT', './fakemail');
@define('FAKEMAIL_PORT', 25);
@define('FAKEMAIL_HOST', 'localhost');
@define('FAKEMAIL_PATH', '.');

class FakemailDaemon
{
  var $pid = null;
  var $fakemail = null;
  var $mail_path = null;
  var $port = null;
  var $host = null;
  var $log_path  = null;

  function FakemailDaemon($fakemail = null, $mail_path = null, $port = null, $host = null)
  {
    $this->fakemail = is_null($fakemail) ?  FAKEMAIL_SCRIPT : $fakemail;
    $this->mail_path = is_null($mail_path) ?  FAKEMAIL_PATH : $mail_path;
    $this->port = is_null($port) ?  FAKEMAIL_PORT : $port;
    $this->host = is_null($host) ?  FAKEMAIL_HOST : $host;
  }

  function useLog($log_path = null)
  {
    $this->log_path = is_null($log_path) ?  $this->mail_path . '/fakemail.log' : $log_path;
  }

  function start()
  {
    if(!file_exists($this->fakemail))
      die('fakemail script "'. $this->fakemail .'" not found');

    if(!file_exists($this->mail_path) || !is_dir($this->mail_path))
      die('Directory for fake mails "'. $this->mail_path .'" not found' );

    $cmd = "perl ". $this->fakemail ." --background --path={$this->mail_path} --port={$this->port} --host={$this->host}" ;

    if($this->log_path)
      $cmd .= " --log={$this->log_path}";

    $this->pid = exec($cmd, $out);

    if(!$this->pid)
      die('fakemail script has not started for some reason, here is the command line: ' . $cmd);
  }

  function stop()
  {
    if($this->pid)
      exec("kill {$this->pid}");
  }

  function clearLog()
  {
     unlink($this->log_path);
  }

  function removeRecipientMail($recipient)
  {
     $names = $this->_getRecipientFileNames($recipient);
     foreach($names as $name)
       unlink($this->path .'/'. $name);
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
       $contents[] = file_get_contents($this->mail_path .'/'. $name);
     return $contents;
  }

  function _getRecipientFileNames($recipient)
  {
    $saved_working_dir = getcwd();
    $recipient_files = array();

    if (is_dir($this->mail_path))
    {
      chdir($this->mail_path);
      $handle = opendir('.');
      while (($file = readdir($handle)) !== false)
      {
        if ($file == "." || $file == ".." || $file == '.svn' || is_dir($file))
          continue;

        if (is_file($file) && strpos($file, $recipient . '.') !== false)
          $recipient_files[] = $file;

      }
      closedir($handle);
    }

    chdir($saved_working_dir);
    array_multisort($recipient_files, SORT_ASC, SORT_STRING);
    return $recipient_files;
  }
}
?>