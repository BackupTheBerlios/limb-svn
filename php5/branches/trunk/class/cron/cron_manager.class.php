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
include_once(LIMB_DIR . '/class/lib/util/ini.class.php');

class cron_manager
{
  protected $jobs = array();
  protected $jobs_last_time = array();
        
  public function get_jobs()
  {
    if(!$this->jobs)
      $this->_load_jobs();
    
    return $this->jobs;
  }
  
  protected function _load_jobs()
  {
    $this->jobs = array();
    
    $groups = get_ini('cron.ini')->get_all();

    foreach($groups as $group => $data)
    {
      if(strpos($group, 'cron-job') === 0)
        $this->jobs[$group] = $data;
    }
  }
  
  public function get_jobs_last_time()
  {
    if(!$this->jobs_last_time)
      $this->_load_jobs_last_time();
      
    return $this->jobs_last_time;
  }

  protected function _load_jobs_last_time()
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
  
  protected function _get_time()
  {
    return time();
  }
  
  public function perform($response, $force=false)
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
        
        $object = $this->_create_job_object($handle);
        
        $object->set_response($response);
        $object->perform();
        
        $response->write("script done\n");
        
        $this->_set_job_last_time($key, $now);
      }
    }
    
    $this->_write_jobs_last_time();
  }
  
  protected function _create_job_object($handle)
  {
    resolve_handle($handle);
    return $handle;
  }
  
  protected function _set_job_last_time($key, $time)
  {
    $this->jobs_last_time[$key] = $time;
  }
  
  protected function _write_jobs_last_time()
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