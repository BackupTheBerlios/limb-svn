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
require_once(LIMB_DIR . '/class/datasources/datasource.interface.php');

class poll_all_results_datasource implements datasource
{
  public function get_dataset(&$counter, $params = array())
  {
    $questions = $this->_load_all_questions();

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

    $counter = sizeof($questions);
    return new array_dataset($questions);
  }

  protected function _load_all_questions()
  {
    $datasource = Limb :: toolkit()->getDatasource('site_objects_branch_datasource');
    $datasource->set_path('root/polls');
    $datasource->set_site_object_class_name('poll');
    $datasource->set_restrict_by_class();

    return $datasource->fetch();
  }

  protected function _load_answers($question_path)
  {
    $datasource = Limb :: toolkit()->getDatasource('site_objects_branch_datasource');
    $datasource->set_path($question_path);
    $datasource->set_site_object_class_name('poll_answer');
    $datasource->set_restrict_by_class();

    return $datasource->fetch();
  }
}


?>