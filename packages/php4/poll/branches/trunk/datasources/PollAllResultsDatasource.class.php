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

class PollAllResultsDatasource// implements Datasource
{
  function getDataset(&$counter, $params = array())
  {
    $questions = $this->_loadAllQuestions();

    if(!count($questions))
      return new ArrayDataset(array());

    foreach($questions as $key => $data)
    {
      $questions[$key]['answers'] = $this->_loadAnswers($data['path']);

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
    return new ArrayDataset($questions);
  }

  function _loadAllQuestions()
  {
    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('SiteObjectsBranchDatasource');
    $datasource->setPath('root/polls');
    $datasource->setSiteObjectClassName('poll');
    $datasource->setRestrictByClass();

    return $datasource->fetch();
  }

  function _loadAnswers($question_path)
  {
    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('SiteObjectsBranchDatasource');
    $datasource->setPath($question_path);
    $datasource->setSiteObjectClassName('poll_answer');
    $datasource->setRestrictByClass();

    return $datasource->fetch();
  }
}


?>