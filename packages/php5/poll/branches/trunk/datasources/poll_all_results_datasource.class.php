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
require_once(LIMB_DIR . 'class/datasources/datasource.class.php');

class poll_all_results_datasource extends datasource
{
	public function get_dataset(& $counter, $params = array())
	{
		$questions = $this->_load_all_questions($params);
		
		if(!count($questions))
			return new array_dataset(array());
			
		foreach($questions as $key => $data)
		{
			$questions[$key]['answers'] = $this->_load_answers($data['path']);
			
			$questions[$key]['total_count'] = 0;

			if(!count($questions[$key]['answers']))
			{
				$questions[$key]['total_count'] = 0;
				continue;
			}	

			foreach($questions[$key]['answers'] as $answer_id => $answer_data)
				$questions[$key]['total_count'] += $answer_data['count'];
			
			foreach($questions[$key]['answers'] as $answer_id => $answer_data)
			{
				if ($questions[$key]['total_count'] == 0)
				{
					$questions[$key]['answers'][$answer_id]['percentage'] = 0;
					$questions[$key]['answers'][$answer_id]['rounded_percentage'] = 0;
				}
				else
				{
					$questions[$key]['answers'][$answer_id]['percentage'] = round($answer_data['count'] / $questions[$key]['total_count']*100, 2);
					$questions[$key]['answers'][$answer_id]['rounded_percentage'] = round($answer_data['count'] / $questions[$key]['total_count']*100);
				}
			}	
		}	

		return new array_dataset($questions);
	}
	
	private function _load_all_questions($new_params = array())
	{
		$params = array(
			'depth' => -1
		);
		
		$params = complex_array :: array_merge($params, $new_params);
		
		return fetch_sub_branch('/root/polls', 'poll', $params);
	}
	
	private function _load_answers($question_path)
	{
		$params = array(
			'depth' => 1
		);
		
		return fetch_sub_branch($question_path, 'poll_answer', $params);
	}
}


?>