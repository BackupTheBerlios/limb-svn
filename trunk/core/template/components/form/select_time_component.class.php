<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class select_time_component extends form_component
{
	var $select_hour = null;
	var $select_minute = null;
	var $select_second = null;
	var $selected_time = array();

	var $set_default_selection = false;
	var $as_array = false;
	var $group_name;

	/**
	* the compiler complains if not defined...
	*/
	function is_visible()
	{
		return true;
	} 

	function set_group_name($name)
	{
		$this->group_name = $name;
	} 

	function set_as_array()
	{
		$this->as_array = $this->get_attribute('as_array');
	} 

	/**
	* 
	* @param mixed $ int|string unix timestamp or ISO-8601 timestamp
	* @return array 
	* @access private 
	*/
	function parse_time($time = null)
	{
		if (is_integer($time))
		{ 
			// $time = unix timestamp
			return array(
				'hour' => date('H', $time),
				'minute' => date('i', $time),
				'second' => date('s', $time)
			);
		} 

		if (is_string($time) && strlen($time) == 14)
		{ 
			// $time = mysql timestamp YYYYMMDDHHMMSS
			return array(
				'hour' => (int)substr($time, 8, 2),
				'minute' => (int)substr($time, 10, 2),
				'second' => (int)substr($time, 12, 2),
			);
		} 

		if (is_string($time) && strlen($time) == 10)
		{ 
			// $time = ISO-8601 timestamp YYYY-MM-DD HH:MM:SS
			return array(
				'hour' => (int)substr($time, 11, 2),
				'minute' => (int)substr($time, 14, 2),
				'second' => (int)substr($time, 17, 2),
			);
		} 
		// if everything failed, try with strtotime
		if (empty($time))
		{
			$time = 'now';
		} 
		$time = strtotime($time);
		if (!is_numeric($time) || $time == -1)
		{
			$time = strtotime('now');
		} 
		return array(
			'hour' => date('H', $time),
			'minute' => date('i', $time),
			'second' => date('s', $time)
		);
	} 

	function prepare_hour()
	{
		$this->select_hour = new select_single_component();
		$this->add_child($this->select_hour);

		$use24hours = ($this->has_attribute('use24hours') ? $this->get_attribute('use24hours') : true);

		$hours = array();
		$end = ($use24hours ? 24 : 12);
		for ($i = 1; $i <= $end; $i++)
		{
			$hours[sprintf('%02d', $i)] = sprintf('%02d', $i);
		} 
		$this->select_hour->set_choices($hours);
		if ($this->set_default_selection)
		{
			$this->select_hour->set_selection(($this->selected_time['hour'] % $end));
		} 
		// maintain selection through pages
		$form_component = &$this->find_parent_by_class('form_component');
		if ($h = $form_component->get($this->group_name . '_hour'))
		{
			$this->select_hour->set_selection($h);
		} 
		if ($date = $form_component->get($this->group_name))
		{
			if (is_array($date) && array_key_exists('hour', $date))
			{
				$this->select_hour->set_selection($date['hour']);
			} 
			else
			{
				$this->selected_time = $this->parse_time($date);
				$this->select_hour->set_selection($this->selected_time['hour']);
			} 
		} 
	} 

	function prepare_minute()
	{
		$this->select_minute = new select_single_component();
		$this->add_child($this->select_minute);

		$minutes = array();
		for ($i = 1; $i <= 60; $i++)
		{
			$minutes[sprintf('%02d', $i)] = sprintf('%02d', $i);
		} 
		$this->select_minute->set_choices($minutes);
		if ($this->set_default_selection)
		{
			$this->select_minute->set_selection($this->selected_time['minute']);
		} 
		// maintain selection through pages
		$form_component = &$this->find_parent_by_class('form_component');
		if ($m = $form_component->get($this->group_name . '_minute'))
		{
			$this->select_minute->set_selection($m);
		} 
		if ($date = $form_component->get($this->group_name))
		{
			if (is_array($date) && array_key_exists('minute', $date))
			{
				$this->select_minute->set_selection($date['minute']);
			} 
			else
			{
				$this->selected_time = $this->parse_time($date);
				$this->select_minute->set_selection($this->selected_time['minute']);
			} 
		} 
	} 

	function prepare_second()
	{
		$this->select_second = new select_single_component();
		$this->add_child($this->select_second);

		$seconds = array();
		for ($i = 1; $i <= 60; $i++)
		{
			$seconds[sprintf('%02d', $i)] = sprintf('%02d', $i);
		} 
		$this->select_second->set_choices($seconds);
		if ($this->set_default_selection)
		{
			$this->select_second->set_selection($this->selected_time['second']);
		} 
		// maintain selection through pages
		$form_component = &$this->find_parent_by_class('form_component');
		if ($s = $form_component->get($this->group_name . '_second'))
		{
			$this->select_second->set_selection($s);
		} 
		if ($date = $form_component->get($this->group_name))
		{
			if (is_array($date) && array_key_exists('second', $date))
			{
				$this->select_second->set_selection($date['second']);
			} 
			else
			{
				$this->selected_time = $this->parse_time($date);
				$this->select_second->set_selection($this->selected_time['second']);
			} 
		} 
	} 

	function set_selection($time = null)
	{
		if (is_null($time))
		{
			$time = time();
		} 
		$this->selected_time = $this->parse_time($time);
		$this->set_default_selection = true;
	} 

	function &get_hour()
	{
		return $this->select_hour;
	} 

	function &get_minute()
	{
		return $this->select_minute;
	} 

	function &get_second()
	{
		return $this->select_second;
	} 
} 

?>