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
require_once(LIMB_DIR . '/core/cron/CronManager.class.php');
require_once(LIMB_DIR . '/core/cron/cronjobs/CronjobCommand.class.php');
require_once(LIMB_DIR . '/core/request/Response.interface.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');

Mock :: generate('CronjobCommand');
Mock :: generate('Response');
Mock :: generate('LimbToolkit');

Mock :: generatePartial(
  'CronManager',
  'CronManagerTestVersion',
  array('getJobs', '_getTime', '_createJobObject')
);

Mock :: generatePartial(
  'CronManager',
  'CronManagerTestVersion2',
  array('getJobs', '_getTime')
);

class CronManagerTest extends LimbTestCase
{
  var $cron_manager;
  var $response;
  var $cron_job;

  function setUp()
  {
    $this->cron_manager = new CronManagerTestVersion($this);
    $this->response = new MockResponse($this);
    $this->cron_job = new MockCronjobCommand($this);
  }

  function tearDown()
  {
    $this->cron_manager->tally();
    $this->response->tally();
    $this->cron_job->tally();
    $this->_removeJobsLastTime();
  }

  function testGetJobsLastTime()
  {
    $this->_writeJobsLastTime(
    'handle1 = 10
     handle3=
     handle

     handle2=20
    '
    );

    $cron_manager = new CronManager();

    $this->assertEqual(
      array('handle1' => 10, 'handle2' => 20),
      $cron_manager->getJobsLastTime()
    );
  }

  function testGetJobsFromIni()
  {
    $cron_manager = new CronManager();

    registerTestingIni(
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

    $this->assertEqual($cron_manager->getJobs(),
      array(
        'cron-job1' => array('handle' => 'test1.php', 'interval' => 10),
        'cron-job2' => array('handle' => 'test2.php', 'interval' => 20)
      )
    );

    clearTestingIni();
  }

  function testPerformThereIsNoJobsLastTimeFile()
  {
    $job1 = array('handle' => 'test1', 'interval' => 10);
    $job2 = array('handle' => 'test2', 'interval' => 10);

    $this->cron_manager->setReturnValue('getJobs',
      array(
        'cron-job1' => $job1,
        'cron-job2' => $job2
      )
    );

    $this->cron_job->expectAtLeastOnce('setResponse', array(new IsAExpectation('MockResponse')));
    $this->cron_job->expectAtLeastOnce('perform');
    $this->cron_manager->setReturnReference('_createJobObject', $this->cron_job);

    $this->cron_manager->setReturnValue('_getTime', 1);

    $this->cron_manager->expectArgumentsAt(0, '_createJobObject', array(new LimbHandle('test1')));
    $this->cron_manager->expectArgumentsAt(1, '_createJobObject', array(new LimbHandle('test2')));

    $this->cron_manager->perform($this->response);

    $contents = $this->_readJobsLastTime();

    $this->assertEqual("cron-job1 = 1\ncron-job2 = 1", $contents);
  }

  function testPerformWithJobsLastTimeFileAllEntries()
  {
    $this->_writeJobsLastTime("cron-job1 = 1 \n cron-job2 = 11");

    $job1 = array('handle' => 'test1', 'interval' => 10);
    $job2 = array('handle' => 'test2', 'interval' => 10);

    $this->cron_manager->setReturnValue('getJobs',
      array(
        'cron-job1' => $job1,
        'cron-job2' => $job2
      )
    );

    $this->cron_job->expectOnce('setResponse', array(new IsAExpectation('MockResponse')));
    $this->cron_job->expectOnce('perform');
    $this->cron_manager->setReturnReference('_createJobObject', $this->cron_job);

    $this->cron_manager->setReturnValue('_getTime', 12);

    $this->cron_manager->expectCallCount('_createJobObject', 1);
    $this->cron_manager->expectArgumentsAt(0, '_createJobObject', array(new LimbHandle('test1')));

    $this->cron_manager->perform($this->response);

    $contents = $this->_readJobsLastTime();

    $this->assertEqual("cron-job1 = 12\ncron-job2 = 11", $contents);
  }

  function testPerformWithJobsLastTimeFileMissingEntry()
  {
    $this->_writeJobsLastTime("cron-job1 = 1");

    $job1 = array('handle' => 'test1', 'interval' => 10);
    $job2 = array('handle' => 'test2', 'interval' => 10);

    $this->cron_manager->setReturnValue('getJobs',
      array(
        'cron-job1' => $job1,
        'cron-job2' => $job2
      )
    );

    $this->cron_job->expectAtLeastOnce('setResponse', array(new IsAExpectation('MockResponse')));
    $this->cron_job->expectAtLeastOnce('perform');
    $this->cron_manager->setReturnReference('_createJobObject', $this->cron_job);

    $this->cron_manager->setReturnValue('_getTime', 12);
    $this->cron_manager->expectArgumentsAt(0, '_createJobObject', array(new LimbHandle('test1')));
    $this->cron_manager->expectArgumentsAt(1, '_createJobObject', array(new LimbHandle('test2')));

    $this->cron_manager->perform($this->response);

    $contents = $this->_readJobsLastTime();

    $this->assertEqual("cron-job1 = 12\ncron-job2 = 12", $contents);
  }

  function testPerformWithIntervalMissingEntry()
  {
    $this->_writeJobsLastTime("cron-job1 = 1");

    $job1 = array('handle' => 'test1');

    $this->cron_manager->setReturnValue('getJobs',
      array(
        'cron-job1' => $job1,
      )
    );

    $this->cron_job->expectAtLeastOnce('setResponse', array(new IsAExpectation('MockResponse')));
    $this->cron_job->expectAtLeastOnce('perform');
    $this->cron_manager->setReturnReference('_createJobObject', $this->cron_job);

    $this->cron_manager->setReturnValue('_getTime', 12);
    $this->cron_manager->expectArgumentsAt(0, '_createJobObject', array(new LimbHandle('test1')));

    $this->cron_manager->perform($this->response);

    $contents = $this->_readJobsLastTime();

    $this->assertEqual("cron-job1 = 12", $contents);
  }

  function testForcedPerform()
  {
    $this->_writeJobsLastTime("cron-job1 = 10000");

    $job = array('handle' => 'test1.php', 'interval' => 10000);

    $this->cron_manager->setReturnValue('getJobs',
      array(
        'cron-job1' => $job,
      )
    );

    $this->cron_manager->setReturnValue('_getTime', 10001);
    $this->cron_manager->setReturnReference('_createJobObject', $this->cron_job);
    $this->cron_manager->perform($this->response, true);

    $contents = $this->_readJobsLastTime();

    $this->assertEqual("cron-job1 = 10001", $contents);
  }

  function testPreformTestingCronJob()
  {
    $job = array('handle' => dirname(__FILE__) . '/TestingCronJob', 'interval' => 1);

    $cron_manager = new CronManagerTestVersion2($this);

    $cron_manager->setReturnValue('getJobs',
      array('cron-job' => $job)
    );

    $cron_manager->setReturnValue('_getTime', 1);

    $this->response->expectArgumentsAt(1, 'write', array('I was performed'));

    $cron_manager->perform($this->response);
  }

  function testRealPerform()
  {
    $handle = dirname(__FILE__) . '/TestingCronJob';
    registerTestingIni(
      'cron.ini',
      "
      [cron-job1]
       handle = {$handle}
      "
    );

    $cron_manager = new CronManager();

    $this->response->expectArgumentsAt(1, 'write', array('I was performed'));

    $cron_manager->perform($this->response);

    $contents = $this->_readJobsLastTime();

    $this->assertWantedPattern("/^cron-job1 = \d+$/", $contents);
  }

  function _writeJobsLastTime($content)
  {
    $f = fopen(VAR_DIR . '.cronjobs', 'w');
    fwrite($f, $content);
    fclose($f);
  }

  function _readJobsLastTime()
  {
    if(!file_exists(VAR_DIR . '.cronjobs'))
      return '';

    return trim(file_get_contents(VAR_DIR . '.cronjobs'));
  }

  function _removeJobsLastTime()
  {
    if(file_exists(VAR_DIR . '.cronjobs'))
      unlink(VAR_DIR . '.cronjobs');
  }

}

?>