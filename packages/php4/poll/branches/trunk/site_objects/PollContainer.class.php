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
require_once(LIMB_DIR . '/class/site_objects/SiteObject.class.php');

class PollContainer extends SiteObject
{
  function canVote()
  {
    if(!$poll_data = $this->getActivePoll())
      return false;

    $poll_id = $poll_data['id'];

    if(defined('DEBUG_POLL_ENABLED') &&  constant('DEBUG_POLL_ENABLED'))
      return true;

    $toolkit =& Limb :: toolkit();
    $session =& $toolkit->getSession();

    $poll_session = $session->get('poll_session');
    if (is_array($poll_session) &&  isset($poll_session[$poll_id]))
      return false;

    switch($poll_data['restriction'])
    {
      case 1:
        return true;
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

      case 3:
        if ($this->_pollIpExists($poll_id, Sys :: clientIp()))
          return false;
      break;
    }

    return true;
  }

  function registerAnswer($answer_id)
  {
    if (!$answer_id)
      return false;

    if(!$poll_data = $this->getActivePoll())
      return false;

    $poll_id = $poll_data['id'];
    if (!$poll_id)
      return false;

    if (!$this->canVote())
      return false;

    if(!$this->_addVoteToAnswer($poll_data['answers'][$answer_id]['record_id']))
      return false;

    $toolkit =& Limb :: toolkit();
    $session =& $toolkit->getSession();

    $poll_session =& $session->getReference('poll_session');
    $poll_session[$poll_id] = $poll_id;

    switch($poll_data['restriction'])
    {
      case 1:
        return true;
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

      case 3:
        $this->_registerNewIp($poll_id, Sys :: clientIp());
      break;
    }

    return true;
  }

  function _addVoteToAnswer($record_id)
  {
    $toolkit =& Limb :: toolkit();
    $poll_answer_db_table =& $toolkit->createDBTable('PollAnswer');

    $data = $poll_answer_db_table->getRowById($record_id);
    if (!$data)
      return false;

    $data['count'] = $data['count'] + 1;
    $poll_answer_db_table->update($data, 'id=' . $record_id);

    return true;
  }


  function _registerNewIp($poll_id, $ip)
  {
    $toolkit =& Limb :: toolkit();
    $poll_ip_db_table =& $toolkit->createDBTable('PollIp');
    $data['id'] = null;
    $data['ip'] = $ip;
    $data['poll_id'] = $poll_id;
    $poll_ip_db_table->insert($data);
  }

  function _pollIpExists($poll_id, $ip)
  {
    $toolkit =& Limb :: toolkit();
    $poll_ip_db_table =& $toolkit->createDBTable('PollIp');
    $where['poll_id'] = $poll_id;
    $where['ip'] = $ip;

    if ($poll_ip_db_table->getList($where))
      return true;
    else
      return false;
  }

  function getActivePoll()
  {
    if(!$questions = $this->_loadAllQuestions())
      return array();

    $current_date = date('Y-m-d', time());

    foreach($questions as $key => $data)
    {
      if (($data['start_date'] > $current_date) ||  ($data['finish_date'] < $current_date))
        unset($questions[$key]);
    }

    if (!count($questions))
      return array();

    $record = reset($questions);
    $this->_processQuestion($record);

    return $record;
  }

  function _processQuestion(& $poll_data)
  {
    $poll_data['answers'] = $this->_loadAnswers($poll_data['path']);

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

  function _loadAllQuestions()
  {
    $toolkit =& Limb :: toolkit();
    $datasource =& $toolkit->getDatasource('SiteObjectsBranchDatasource');

    $datasource->setPath('/root/polls');
    $datasource->setOrder(array('start_date' => 'DESC'));
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