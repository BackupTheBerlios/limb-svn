<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CrudMainBehaviour.class.php 23 2005-02-26 18:11:24Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/behaviours/Behaviour.class.php');

class StatsReportsBehaviour extends Behaviour
{
  function _defineDefaultAction()
  {
    return 'adminDisplay';
  }

  function defineAdminDisplay(&$state_machine)
  {
    $state_machine->registerState('init',
                                  new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                                 array('/stats/display.html')),
                                  array(LIMB_STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  new LimbHandle(LIMB_DIR . '/core/commands/DisplayViewCommand'));
  }

  function definePagesReport(&$state_machine)
  {
    $state_machine->registerState('init',
                                  new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                                 array('/stats/pages_report.html')),
                                  array(LIMB_STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  new LimbHandle(LIMB_DIR . '/core/commands/DisplayViewCommand'));
  }

  function defineReferersReport(&$state_machine)
  {
    $state_machine->registerState('init',
                                  new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                                 array('/stats/referers_report.html')),
                                  array(LIMB_STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  new LimbHandle(LIMB_DIR . '/core/commands/DisplayViewCommand'));
  }

  function defineCountersReport(&$state_machine)
  {
    $state_machine->registerState('init',
                                  new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                                 array('/stats/counters_report.html')),
                                  array(LIMB_STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  new LimbHandle(LIMB_DIR . '/core/commands/DisplayViewCommand'));
  }

  function defineIpsReport(&$state_machine)
  {
    $state_machine->registerState('init',
                                  new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                                 array('/stats/ips_report.html')),
                                  array(LIMB_STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  new LimbHandle(LIMB_DIR . '/core/commands/DisplayViewCommand'));
  }

  function defineKeywordsReport(&$state_machine)
  {
    $state_machine->registerState('init',
                                  new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                                 array('/stats/keywords_report.html')),
                                  array(LIMB_STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  new LimbHandle(LIMB_DIR . '/core/commands/DisplayViewCommand'));
  }

  function definesSearchEnginesReport(&$state_machine)
  {
    $state_machine->registerState('init',
                                  new LimbHandle(LIMB_DIR . '/core/commands/UseViewCommand',
                                                 array('/stats/search_engines_report.html')),
                                  array(LIMB_STATUS_OK => 'render'));

    $state_machine->registerState('render',
                                  new LimbHandle(LIMB_DIR . '/core/commands/DisplayViewCommand'));
  }

}

?>