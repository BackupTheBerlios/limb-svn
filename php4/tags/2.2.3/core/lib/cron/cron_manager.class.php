<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
include_once(LIMB_DIR . '/core/lib/util/ini.class.php');

class cron_manager
{
  var $jobs = array();
  var $jobs_last_time = array();
  
  function cron_manager()
  {
  }
      
  function get_jobs()
  {
    if(!$this->jobs)
      $this->_load_jobs();
    
    return $this->jobs;
  }
  
  function _load_jobs()
  {
    $ini =& get_ini('cron.ini');
    $this->jobs = array();
    
    $groups = $ini->get_all();

    foreach($groups as $group => $data)
    {
      if(strpos($group, 'cron-job') === 0)
        $this->jobs[$group] = $data;
    }
  }
  
  function get_jobs_last_time()
  {
    if(!$this->jobs_last_time)
      $this->_load_jobs_last_time();
      
    return $this->jobs_last_time;
  }

  function _load_jobs_last_time()
  {
    $this->jobs_last_time = array();
    
    if(!file_exists(VAR_DIR . '.cronjobs'))
      return;
    
    $contents = explode("\n", file_get_contents(VAR_DIR . '.cronjobs')); 
    
    if(!$contents)
      return;
    
    foreach($contents as $content)
    {
      if(!($pieces = explode('=', trim($content))))
        continue;
      
      if(isset($pieces[1]) && is_numeric(trim($pieces[1])))
        $this->jobs_last_time[trim($pieces[0])] = (int)trim($pieces[1]);
    } 
  }  
  
  function _get_time()
  {
    return time();
  }
  
  function perform(&$response, $force=false)
  {
    $now = $this->_get_time();
    $jobs = $this->get_jobs();
    
    if($force)
      $last_time = array();
    else
      $last_time = $this->get_jobs_last_time();
    
    foreach($jobs as $key => $job)
    {
      if(isset($last_time[$key]))
        $time_diff = $now - $last_time[$key];
      else
        $time_diff = -1;
        
      if($time_diff == -1 || !isset($job['interval']) || $time_diff > $job['interval'])
      {
        $handle = $job['handle'];
        
        $response->write("hadle {$handle} starting\n");
        
        $object =& $this->_create_job_object($handle);
        
        $object->perform($response);
        
        $response->write("script done\n");
        
        $this->_set_job_last_time($key, $now);
      }
    }
    
    $this->_write_jobs_last_time();
  }
  
  function &_create_job_object($handle)
  {
    $object = $handle;
    resolve_handle($object);
    
    return $object;
  }
  
  function _set_job_last_time($key, $time)
  {
    $this->jobs_last_time[$key] = $time;
  }
  
  function _write_jobs_last_time()
  {
    $f = fopen(VAR_DIR . '.cronjobs', 'w');
    
    foreach($this->jobs_last_time as $key => $time)
    {
      fwrite($f, "{$key} = {$time}\n");
    }
    
    fclose($f);
  }
  
}

?>