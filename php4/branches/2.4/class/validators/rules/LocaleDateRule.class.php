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
require_once(LIMB_DIR . '/class/validators/rules/DomainRule.class.php');
require_once(LIMB_DIR . '/class/i18n/Locale.class.php');
require_once(LIMB_DIR . '/class/lib/date/Date.class.php');

class LocaleDateRule extends SingleFieldRule
{
  var $locale_id = '';

  function __construct($fieldname, $locale_id = '')
  {
    if (!$locale_id &&  !defined('CONTENT_LOCALE_ID'))
      $this->locale_id = DEFAULT_CONTENT_LOCALE_ID;
    elseif(!$locale_id)
      $this->locale_id = CONTENT_LOCALE_ID;
    else
      $this->locale_id = $locale_id;

    parent :: __construct($fieldname);
  }

  function check($value)
  {
    $date = new Date();
    $locale = Limb :: toolkit()->getLocale($this->locale_id);

    $date->setByLocaleString($locale, $value, $locale->getShortDateFormat());

    if(!$date->isValid())
      $this->error('INVALID_DATE');
  }
}

?>