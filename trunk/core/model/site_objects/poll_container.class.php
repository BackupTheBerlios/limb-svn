<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: poll_container.class.php 470 2004-02-18 13:04:56Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');

class poll_container extends site_object
{
	function poll_container()
	{
		parent :: site_object();
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 0,
			'can_be_parent' => 1,
			'controller_class_name' => 'poll_container_controller',
			'icon' => '/shared/images/folder.gif'
		);
	}
	
	function can_vote()
	{
		$poll_data = $this->get_active_poll();
		if(!count($poll_data))
			return false;
			
		$poll_id = $poll_data['id'];

		if(defined('POLL_DEBUG_ENABLED'))
			return true;

		$poll_session =& session :: get('poll_session');
		if (is_array($poll_session) && isset($poll_session[$poll_id]))
			return false;
		
		switch($poll_data['restriction'])
		{
			case 1:
				return true;
			break;
			
			case 3:
				if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
					$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
				
				if ($this->_is_poll_ip_exists($poll_id, $_SERVER['REMOTE_ADDR']))
					return false;
			break;
			
			case 2:
				$cookie = $_COOKIE;
				if (!isset($cookie['poll_ids']))
					return true;
					
				$poll_ids = $cookie['poll_ids'];
				$ips = explode(',', $poll_ids);
				foreach($ips as $id => $data)
				{
					if ($data == $poll_id)
						return false;
				}
			break;
		}

		return true;
	}

	function register_answer($answer_id)
	{
		if (!$answer_id)
			return false;

		$poll_data = $this->get_active_poll();
		if(!count($poll_data))
			return false;
	
		$poll_id = $poll_data['id'];
		if (!$poll_id)
			return false;

		if (!$this->can_vote())
			return false;
			
		if(!$this->_add_vote_to_answer($poll_data['answers'][$answer_id]['record_id']))
			return false;
		
		$poll_session =& session :: get('poll_session');
		$poll_session[$poll_id] = $poll_id;
		
		switch($poll_data['restriction'])
		{
			case 1:
				return true;
			break;
			
			case 3:
				if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
					$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
				
				$this->_register_new_ip($poll_id, $_SERVER['REMOTE_ADDR']);
			break;
			
			case 2:
				$cookie = $_COOKIE;
				if (isset($cookie['poll_ids']))
				{
					$poll_ids = $cookie['poll_ids'];
					$poll_ids += ','. $poll_id;
				}
				else
				{
					$poll_ids = $poll_id;
				}
				
				$one_week = 7 * 24 * 60 * 60;
				setcookie('poll_ids', $poll_ids, time() + $one_week, '/');
			break;
		}
		
		return true;
	}
	
	function _add_vote_to_answer($record_id)
	{
		$poll_answer_db_table = & db_table_factory :: instance('poll_answer');
		
		$data = $poll_answer_db_table->get_row_by_id($record_id);
		if (!$data)
			return false;
		
		$data['count'] = $data['count'] + 1;
		$poll_answer_db_table->update($data, 'id=' . $record_id);
		
		return true;
	}

	
	function _register_new_ip($poll_id, $ip)
	{
		$poll_ip_db_table = & db_table_factory :: instance('poll_ip');
		$data['ip'] = $ip;
		$data['poll_id'] = $poll_id;
		$poll_ip_db_table->insert($data);
	}
	
	function _is_poll_ip_exists($poll_id, $ip)
	{
		$poll_ip_db_table = & db_table_factory :: instance('poll_ip');
		$where['poll_id'] = $poll_id;
		$where['ip'] = $ip;
		$data = $poll_ip_db_table->get_list($where);
		if (count($data))
			return true;
		else
			return false;
	}

	function get_active_poll()
	{
		$questions = $this->_load_all_questions();

		$current_date = date('Y-m-d', time());

		if (!count($questions))
			return array();
		
		foreach($questions as $key => $data)
		{
			if (($data['start_date'] >= $current_date) || ($data['finish_date'] <= $current_date))
				unset($questions[$key]);
		}	

		if (!count($questions))
			return array();
		
		$record =& reset($questions);
		$this->_process_question($record);

		return $record;
	}

	function _process_question(& $poll_data)
	{
		$poll_data['answers'] = $this->_load_answers($poll_data['path']);
		
		$poll_data['total_count'] = 0;
		foreach($poll_data['answers'] as $answer_id => $answer_data)
			$poll_data['total_count'] += $answer_data['count'];
		
		foreach($poll_data['answers'] as $answer_id => $answer_data)
			if($poll_data['total_count'] > 0 )
			{
				$poll_data['answers'][$answer_id]['percentage'] = round($answer_data['count'] / $poll_data['total_count']*100, 2);
				$poll_data['answers'][$answer_id]['rounded_percentage'] = round($answer_data['count'] / $poll_data['total_count']*100);
			}	
			else
			{
				$poll_data['answers'][$answer_id]['percentage'] = 0;
				$poll_data['answers'][$answer_id]['rounded_percentage'] = 0;
			}	
	}

	function & _load_all_questions($new_params = array())
	{
		$params = array(
			'depth' => -1,
			'order' => array(
				'start_date' => 'DESC'
			)
		);
		
		$params = complex_array :: array_merge($params, $new_params);
		
		$result =& fetch_sub_branch('/root/polls', 'poll', $counter, $params);
		return $result;
	}
	
	function & _load_answers($question_path)
	{
		$result =& fetch_sub_branch($question_path, 'poll_answer', $counter);
		return $result;
	}
		
}

?>