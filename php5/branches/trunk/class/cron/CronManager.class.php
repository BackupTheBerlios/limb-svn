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

class CronManager
{
  protected $jobs = array();
  protected $jobs_last_time = array();

  public function getJobs()
  {
    if(!$this->jobs)
      $this->_loadJobs();

    return $this->jobs;
  }

  protected function _loadJobs()
  {
    $this->jobs = array();

    $groups = Limb :: toolkit()->getINI('cron.ini')->getAll();

    foreach($groups as $group => $data)
    {
      if(strpos($group, 'cron-job') === 0)
        $this->jobs[$group] = $data;
    }
  }

  public function getJobsLastTime()
  {
    if(!$this->jobs_last_time)
      $this->_loadJobsLastTime();

    return $this->jobs_last_time;
  }

  protected function _loadJobsLastTime()
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

      if(isset($pieces[1]) &&  is_numeric(trim($pieces[1])))
        $this->jobs_last_time[trim($pieces[0])] = (int)trim($pieces[1]);
    }
  }

  protected function _getTime()
  {
    return time();
  }

  public function perform($response, $force=false)
  {
    $now = $this->_getTime();
    $jobs = $this->getJobs();

    if($force)
      $last_time = array();
    else
      $last_time = $this->getJobsLastTime();

    foreach($jobs as $key => $job)
    {
      if(isset($last_time[$key]))
        $time_diff = $now - $last_time[$key];
      else
        $time_diff = -1;

      if($time_diff == -1 ||  !isset($job['interval']) ||  $time_diff > $job['interval'])
      {
        $handle = $job['handle'];

        $response->write("handle {$handle} starting\n");

        $object = $this->_createJobObject($handle);

        $object->setResponse($response);
        $object->perform();

        $response->write("script done\n");

        $this->_setJobLastTime($key, $now);
      }
    }

    $this->_writeJobsLastTime();
  }

  protected function _createJobObject($handle)
  {
    resolveHandle($handle);
    return $handle;
  }

  protected function _setJobLastTime($key, $time)
  {
    $this->jobs_last_time[$key] = $time;
  }

  protected function _writeJobsLastTime()
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