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
require_once(LIMB_DIR . '/class/cron/cronjobs/CronjobCommand.class.php');

class TestingCronJob extends CronjobCommand
{
  function perform()
  {
    $this->response->write('I was performed');
  }
}

?>