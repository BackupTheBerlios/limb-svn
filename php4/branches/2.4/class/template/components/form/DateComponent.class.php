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
require_once(LIMB_DIR . '/class/template/components/form/InputFormElement.class.php');
require_once(LIMB_DIR . '/class/date/Date.class.php');
require_once(LIMB_DIR . '/class/i18n/Locale.class.php');

class DateComponent extends InputFormElement
{
  function initDate()
  {
    if (defined('DATEBOX_LOAD_SCRIPT'))
      return;

    $date_iframe_id = $this->_getFrameId();

    echo '<iframe width=168 height=190 name="' . $date_iframe_id . '" id="' . $date_iframe_id . '"
          src="/shared/calendar/ipopeng.htm" scrolling="no"
          frameborder="0" style="border:2px ridge; visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;"></iframe>';

    define('DATEBOX_LOAD_SCRIPT',1);
  }

  function _getFrameId()
  {
    //default-month:theme-name[:agenda-file[:context-name[:plugin-file]]]

    if(defined('CONTENT_LOCALE_ID'))
      $locale_id = CONTENT_LOCALE_ID;
    else
      $locale_id = DEFAULT_CONTENT_LOCALE_ID;

    $params = array();

    if($default_month = $this->getAttribute('default_month'))
    {
      $params[0] = $default_month;
      $this->unsetAttribute('default_month');
    }
    else
      $params[0] = 'gToday';

    if($this->getAttribute('theme'))
    {
      $params[1] = $this->getAttribute('theme').'_'. $locale_id;
      $this->unsetAttribute('theme');
    }
    else
      $params[1] = $locale_id;

    if($this->getAttribute('agenda'))
    {
      $params[2] = $this->getAttribute('agenda');
      $this->unsetAttribute('agenda');
    }
    else
      $params[2] = '';

    if($this->getAttribute('context_name'))
    {
      $params[3] = $this->getAttribute('context_name');
      $this->unsetAttribute('context_name');
    }
    else
      $params[3] = '';

    if($this->getAttribute('plugin'))
    {
      $params[4] = $this->getAttribute('plugin');
      $this->unsetAttribute('plugin');
    }

    return implode(':', $params);
  }

  function renderDate()
  {
    $form_id = $this->parent->getAttribute('id');
    $id = $this->getAttribute('id');

    if(!$function = $this->getAttribute('function'))
      $function = "fPopCalendar(document[\"$form_id\"][\"{$id}\"])";
    else
      $this->unsetAttribute('function');

    if(!$button = $this->getAttribute('button'))
      $button = "/shared/calendar/calbtn.gif";
    else
      $this->unsetAttribute('button');

    echo "<a href='javascript:void(0)' onclick='gfPop.". $function .";return false;' HIDEFOCUS><img name='popcal' align='absbottom' src='". $button ."' width='34' height='22' border='0' alt=''></a>";
  }

  function getValue()
  {
    $form = $this->findParentByClass('form_component');

    $value = parent :: getValue();

    if(empty($value))
      $value = $this->getAttribute('default_value');

    if($form->isFirstTime())
    {
      $date = new Date($value);
      $toolkit =& Limb :: toolkit();
      $locale =& $toolkit->getLocale();

      $value = $date->format($locale, $locale->getShortDateFormat());
    }

    return $value;
  }
}
?>