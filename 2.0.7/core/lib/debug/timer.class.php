<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: timer.class.php 367 2004-01-30 14:38:37Z server $
*
***********************************************************************************/ 

class timer
{
	var $markers = array();
	var $group_markers = array();

	function &instance()
	{
		$class_name = 'timer';
		$obj = &$GLOBALS['global_' . $class_name];

		if (get_class($obj) != $class_name)
		{
			$obj = &new $class_name();
			$GLOBALS['global_' . $class_name] = &$obj;
		} 
		return $obj;
	} 

	/**
	* Set marker
	*/
	function set_marker($name, $group_name = 'default')
	{
		static $timer_markers_counter = 0;

		$microtime = explode(' ', microtime());
		$time = $microtime[1] . substr($microtime[0], 1);

		$this->markers[$timer_markers_counter] = $time;

		$group_counter = sizeof($this->group_markers[$group_name]);
		$this->group_markers[$group_name][$group_counter . ') ' . $name] = $timer_markers_counter;

		$timer_markers_counter++;
	} 

	/**
	* Returns profiling information.
	*/
	function get_profiling($group_name = 'default')
	{
		$i = 0;
		$total = 0;
		$result = array();

		$markers = $this->group_markers[$group_name];

		foreach($markers as $marker => $index)
		{
			$time = $this->markers[$index];
			if ($i == 0)
				$diff = 0;
			else
			{
				$temp = $this->markers[$index - 1];
				if (extension_loaded('bcmath'))
				{
					$diff = bcsub($time, $temp, 6);
					$total = bcadd($total, $diff, 6);
				} 
				else
				{
					$diff = $time - $temp;
					$total = $total + $diff;
				} 
			} 

			$result[$i]['name'] = $marker;
			$result[$i]['time'] = $time;
			$result[$i]['diff'] = $diff;
			$result[$i]['total'] = $total;

			$i++;
		} 

		return $result;
	} 

	function render_profiling($user_group_name = '')
	{
		if (!$user_group_name)
			$group_names = array_keys($this->group_markers);
		else
			$group_names[] = $user_group_name;

		$htm = '';

		foreach($group_names as $group_name)
		{
			$profile = $this->get_profiling($group_name);

			$htm .= '<h4>group: ' . $group_name . '</h4><table cellpadding=0 cellspacing=0>';

			$diff = 0;
			$i = 0;

			foreach($profile as $profile_info)
			{
				$diff += $profile_info['diff'];
				$i++;

				$htm .= '<tr>';
				$htm .= '<td valign=top>';
				$htm .= '<b><small>' . $profile_info['name'] . '</small></b>:&nbsp;';
				$htm .= '</td><td>';
				$htm .= '&nbsp;&nbsp;<small>' . $profile_info['diff'] . '</small>';
				$htm .= '</td><td>';
				$htm .= '&nbsp;&nbsp;<small>' . $profile_info['total'] . '</small>';
				$htm .= '</td>';
				$htm .= '</tr>';
			} 

			$htm .= '<tr><td><b><small>avg</small><b>:</td><td>&nbsp;&nbsp;<small>' . ($diff / $i) . '</small></td><td>&nbsp;</td></tr>';
			$htm .= '<tr><td><b><small>total</small><b>:</td><td>&nbsp;&nbsp;<small>' . $profile_info['total'] . '</small></td><td>&nbsp;</td></tr>';
			$htm .= '</table>';
		} 

		return $htm;
	} 

	function render_js_profiling()
	{
		$profile = $this->render_profiling();

		return
		'<script language="javascript">
					params = "top=400,left=500,height=300,width=400,status=no,toolbar=no,menubar=no,location=no,scrollbars=yes,resizable=yes";
					w = window.open("", null, params);
					
					if(w.opener != window)
					{
						w.close();
						w = window.open("", null, params);
					}
					
					w.document.body.innerHTML = "' . $profile . '";
					w.blur();
				</script>';
	} 
} 

?>
