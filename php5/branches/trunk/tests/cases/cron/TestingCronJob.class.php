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
require_once(LIMB_DIR . '/class/cron/cronjobs/cronjob_command.class.php');

class testing_cron_job extends cronjob_command
{
  function perform()
  {
    $this->response->write('I was performed');
  }
}

?>