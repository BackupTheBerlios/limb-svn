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
require_once(LIMB_DIR . '/class/cron/cron_manager.class.php');
require_once(LIMB_DIR . '/class/core/actions/command.interface.php');
require_once(LIMB_DIR . '/class/core/request/response.interface.php');

Mock::generate('command');
Mock::generate('response');

Mock::generatePartial(
  'cron_manager', 
  'cron_manager_test_version', 
  array('get_jobs', '_get_time', '_create_job_object')
);

Mock::generatePartial(
  'cron_manager', 
  'cron_manager_test_version2', 
  array('get_jobs', '_get_time')
);

class cron_manager_test extends LimbTestCase
{
  var $cron_manager;
  var $response;
  var $cron_job;
  
  function setUp()
  {
    $this->cron_manager =& new cron_manager_test_version($this);
    $this->response =& new Mockresponse($this);
    $this->cron_job =& new Mockcommand($this);
  }
  
  function tearDown()
  {
    $this->cron_manager->tally();
    $this->response->tally();
    $this->cron_job->tally();
    $this->_remove_jobs_last_time();
  }
  
  function test_get_jobs_last_time()
  {
    $this->_write_jobs_last_time(
    'handle1 = 10
     handle3=
     handle
     
     handle2=20
    '
    );
    
    $cron_manager = new cron_manager();
    
    $this->assertEqual(
      array('handle1' => 10, 'handle2' => 20),
      $cron_manager->get_jobs_last_time()
    );
  }
  
  function test_get_jobs_from_ini()
  {
    $cron_manager = new cron_manager();
    
    register_testing_ini(
      'cron.ini',
      ' 
      [cron-job1]
       handle = test1.php
       interval = 10
      [cron-job2]
       handle = test2.php
       interval = 20
      [not_valid_cron-job]
       bla-bla = bla-bla
      '
    );

    $this->assertEqual($cron_manager->get_jobs(), 
      array(
        'cron-job1' => array('handle' => 'test1.php', 'interval' => 10),
        'cron-job2' => array('handle' => 'test2.php', 'interval' => 20)
      )
    );
    
    clear_testing_ini();
  }
  
  function test_perform_there_is_no_jobs_last_time_file()
  {
    $job1 = array('handle' => 'test1.php', 'interval' => 10);
    $job2 = array('handle' => 'test2.php', 'interval' => 10);
    
    $this->cron_manager->setReturnValue('get_jobs',
      array(
        'cron-job1' => $job1,
        'cron-job2' => $job2
      )    
    );

    $this->cron_job->expectAtLeastOnce('perform', array(new IsAExpectation('Mockresponse')));
    $this->cron_manager->setReturnReference('_create_job_object', $this->cron_job);
        
    $this->cron_manager->setReturnValue('_get_time', 1);
    
    $this->cron_manager->expectArgumentsAt(0, '_create_job_object', array('test1.php'));
    $this->cron_manager->expectArgumentsAt(1, '_create_job_object', array('test2.php'));
    
    $this->cron_manager->perform($this->response);
    
    $contents = $this->_read_jobs_last_time();
    
    $this->assertEqual("cron-job1 = 1\ncron-job2 = 1", $contents);
  }
  
  function test_perform_with_jobs_last_time_file_all_entries()
  {
    $this->_write_jobs_last_time("cron-job1 = 1 \n cron-job2 = 11");
    
    $job1 = array('handle' => 'test1.php', 'interval' => 10);
    $job2 = array('handle' => 'test2.php', 'interval' => 10);
    
    $this->cron_manager->setReturnValue('get_jobs',
      array(
        'cron-job1' => $job1,
        'cron-job2' => $job2
      )    
    );
    
    $this->cron_job->expectOnce('perform', array(new IsAExpectation('Mockresponse')));
    $this->cron_manager->setReturnReference('_create_job_object', $this->cron_job);
    
    $this->cron_manager->setReturnValue('_get_time', 12);
    
    $this->cron_manager->expectCallCount('_create_job_object', 1);
    $this->cron_manager->expectArgumentsAt(0, '_create_job_object', array('test1.php'));
    
    $this->cron_manager->perform($this->response);
    
    $contents = $this->_read_jobs_last_time();
    
    $this->assertEqual("cron-job1 = 12\ncron-job2 = 11", $contents);
  }  

  function test_perform_with_jobs_last_time_file_missing_entry()
  {
    $this->_write_jobs_last_time("cron-job1 = 1");
    
    $job1 = array('handle' => 'test1.php', 'interval' => 10);
    $job2 = array('handle' => 'test2.php', 'interval' => 10);
    
    $this->cron_manager->setReturnValue('get_jobs',
      array(
        'cron-job1' => $job1,
        'cron-job2' => $job2
      )    
    );

    $this->cron_job->expectAtLeastOnce('perform', array(new IsAExpectation('Mockresponse')));
    $this->cron_manager->setReturnReference('_create_job_object', $this->cron_job);
    
    $this->cron_manager->setReturnValue('_get_time', 12);
    $this->cron_manager->expectArgumentsAt(0, '_create_job_object', array('test1.php'));
    $this->cron_manager->expectArgumentsAt(1, '_create_job_object', array('test2.php'));    
    
    $this->cron_manager->perform($this->response);
    
    $contents = $this->_read_jobs_last_time();
    
    $this->assertEqual("cron-job1 = 12\ncron-job2 = 12", $contents);
  } 

  function test_perform_with_interval_missing_entry()
  {
    $this->_write_jobs_last_time("cron-job1 = 1");
    
    $job1 = array('handle' => 'test1.php');
    
    $this->cron_manager->setReturnValue('get_jobs',
      array(
        'cron-job1' => $job1,
      )    
    );

    $this->cron_job->expectAtLeastOnce('perform', array(new IsAExpectation('Mockresponse')));
    $this->cron_manager->setReturnReference('_create_job_object', $this->cron_job);
    
    $this->cron_manager->setReturnValue('_get_time', 12);
    $this->cron_manager->expectArgumentsAt(0, '_create_job_object', array('test1.php'));
    
    $this->cron_manager->perform($this->response);
    
    $contents = $this->_read_jobs_last_time();
    
    $this->assertEqual("cron-job1 = 12", $contents);
  } 
  
  function test_forced_perform()
  {
    $this->_write_jobs_last_time("cron-job1 = 10000");
    
    $job = array('handle' => 'test1.php', 'interval' => 10000);
    
    $this->cron_manager->setReturnValue('get_jobs',
      array(
        'cron-job1' => $job,
      )    
    );
    
    $this->cron_manager->setReturnValue('_get_time', 10001);
    $this->cron_manager->setReturnReference('_create_job_object', $this->cron_job);
    $this->cron_manager->perform($this->response, true);
    
    $contents = $this->_read_jobs_last_time();
    
    $this->assertEqual("cron-job1 = 10001", $contents);
  }
    
  function test_preform_testing_cron_job()
  {
    $job = array('handle' => dirname(__FILE__) . '/testing_cron_job', 'interval' => 1);
    
    $cron_manager =& new cron_manager_test_version2($this);
    
    $cron_manager->setReturnValue('get_jobs',
      array('cron-job' => $job)    
    );
    
    $cron_manager->setReturnValue('_get_time', 1);
    
    $this->response->expectArgumentsAt(1, 'write', array('I was performed'));
    
    $cron_manager->perform($this->response);
  }
  
  function test_real_perform()
  {
    $handle = dirname(__FILE__) . '/testing_cron_job';
    register_testing_ini(
      'cron.ini',
      " 
      [cron-job1]
       handle = {$handle}
      "
    );
    
    $cron_manager =& new cron_manager();
    
    $this->response->expectArgumentsAt(1, 'write', array('I was performed'));
    
    $cron_manager->perform($this->response);
    
    $contents = $this->_read_jobs_last_time();
    
    $this->assertWantedPattern("/^cron-job1 = \d+$/", $contents);    
  }    
  
  function _write_jobs_last_time($content)
  {
    $f = fopen(VAR_DIR . '.cronjobs', 'w');
    fwrite($f, $content);
    fclose($f);
  }
  
  function _read_jobs_last_time()
  {
    if(!file_exists(VAR_DIR . '.cronjobs'))
      return '';
      
    return trim(file_get_contents(VAR_DIR . '.cronjobs'));
  } 
  
  function _remove_jobs_last_time()
  {
    if(file_exists(VAR_DIR . '.cronjobs'))
      unlink(VAR_DIR . '.cronjobs');
  } 

}

?>