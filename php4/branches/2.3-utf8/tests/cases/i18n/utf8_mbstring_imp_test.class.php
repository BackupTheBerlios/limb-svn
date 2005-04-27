<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: fs_test.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/i18n/utf8_mbstring_imp.class.php');
require_once(dirname(__FILE__) . '/utf8_test.class.php');

class utf8_mbstring_imp_test extends utf8_test
{
  function _create_utf8_imp()
  {
    return new utf8_mbstring_imp();
  }
}

?>