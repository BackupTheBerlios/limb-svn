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

if (!file_exists(SIMPLE_TEST . '/unit_tester.php') )
  die ('Make sure the SIMPLE_TEST constant is set correctly in this file(' . SIMPLE_TEST . ')');

require_once(SIMPLE_TEST . '/unit_tester.php');
require_once(SIMPLE_TEST . '/mock_objects.php');
require_once(SIMPLE_TEST . '/reporter.php');

?>