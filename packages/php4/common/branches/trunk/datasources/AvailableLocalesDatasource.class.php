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
require_once(LIMB_DIR . '/class/i18n/Locale.class.php');

class AvailableLocalesDatasource// implements OptionsDatasource
{
  function getOptionsArray()
  {
    return Locale :: getAvailableLocalesData();
  }

  function getDefaultOption()
  {
    return MANAGEMENT_LOCALE_ID;
  }
}
?>