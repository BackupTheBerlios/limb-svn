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
require_once(WACT_ROOT . '/validation/rule.inc.php');
require_once(LIMB_DIR . '/core/i18n/Locale.class.php');
require_once(LIMB_DIR . '/core/date/Date.class.php');

class LocaleDateRule extends SingleFieldRule
{
  var $locale_id = '';

  function LocaleDateRule($fieldname, $locale_id = '')
  {
    if (!$locale_id &&  !defined('CONTENT_LOCALE_ID'))
      $this->locale_id = DEFAULT_CONTENT_LOCALE_ID;
    elseif(!$locale_id)
      $this->locale_id = CONTENT_LOCALE_ID;
    else
      $this->locale_id = $locale_id;

    parent :: SingleFieldRule($fieldname);
  }

  function check($value)
  {
    $date = new Date();
    $toolkit =& Limb :: toolkit();
    $locale =& $toolkit->getLocale($this->locale_id);

    $date->setByLocaleString($locale, $value, $locale->getShortDateFormat());

    if(!$date->isValid())
      $this->error('INVALID_DATE');
  }
}

?>