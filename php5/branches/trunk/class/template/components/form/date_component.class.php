<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/template/components/form/input_form_element.class.php');
require_once(LIMB_DIR . '/class/lib/date/date.class.php');
require_once(LIMB_DIR . '/class/i18n/locale.class.php');

class date_component extends input_form_element
{
	public function init_date()
	{
		if (defined('DATEBOX_LOAD_SCRIPT'))
			return;
		
		$date_iframe_id = $this->_get_frame_id();

		echo '<iframe width=168 height=190 name="' . $date_iframe_id . '" id="' . $date_iframe_id . '" 
					src="/shared/calendar/ipopeng.htm" scrolling="no" 
					frameborder="0" style="border:2px ridge; visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;"></iframe>';
			
		define('DATEBOX_LOAD_SCRIPT',1);
	}
	
	protected function _get_frame_id()
	{
		//default-month:theme-name[:agenda-file[:context-name[:plugin-file]]]

		if(defined('CONTENT_LOCALE_ID'))
			$locale_id = CONTENT_LOCALE_ID;
		else
			$locale_id = DEFAULT_CONTENT_LOCALE_ID;

		$params = array();

		if($default_month = $this->get_attribute('default_month'))
		{
			$params[0] = $default_month;
			$this->unset_attribute('default_month');
		}
		else
			$params[0] = 'gToday';

		if($this->get_attribute('theme'))
		{
			$params[1] = $this->get_attribute('theme').'_'. $locale_id;
			$this->unset_attribute('theme');
		}
		else
			$params[1] = $locale_id;
			
		if($this->get_attribute('agenda'))
		{
			$params[2] = $this->get_attribute('agenda');
			$this->unset_attribute('agenda');
		}
		else
			$params[2] = '';
			
		if($this->get_attribute('context_name'))
		{
			$params[3] = $this->get_attribute('context_name');
			$this->unset_attribute('context_name');
		}
		else
			$params[3] = '';

		if($this->get_attribute('plugin'))
		{
			$params[4] = $this->get_attribute('plugin');
			$this->unset_attribute('plugin');
		}
		
		return implode(':', $params);
	}
	
	public function render_date()
	{
		$form_id = $this->parent->get_attribute('id');
  	$id = $this->get_attribute('id');
  	
  	if(!$function = $this->get_attribute('function'))
  		$function = "fPopCalendar(document[\"$form_id\"][\"{$id}\"])";
  	else
  		$this->unset_attribute('function');

  	if(!$button = $this->get_attribute('button'))
  		$button = "/shared/calendar/calbtn.gif";
  	else
  		$this->unset_attribute('button');
  	
		echo "<a href='javascript:void(0)' onclick='gfPop.". $function .";return false;' HIDEFOCUS><img name='popcal' align='absbottom' src='". $button ."' width='34' height='22' border='0' alt=''></a>";
	}
	
	public function get_value()
	{		
		$form = $this->find_parent_by_class('form_component');
		
		$value = parent :: get_value();

		if(empty($value))
			$value = $this->get_attribute('default_value');		
		
		if($form->is_first_time())
		{				
			$date = new date($value);
			$locale = Limb :: toolkit()->getLocale();
      
			$value = $date->format($locale, $locale->get_short_date_format());
		}
			
		return $value;
	}
} 
?>