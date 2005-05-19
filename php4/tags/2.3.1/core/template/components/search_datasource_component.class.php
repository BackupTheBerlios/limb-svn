<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(SEARCH_SPIDER_DIR . '/SearchResultProcessor.class.php');
require_once(LIMB_DIR . '/core/template/components/datasource_component.class.php');

class search_datasource_component extends datasource_component
{
  var $processor = null;
  var $left_mark = '';
  var $right_mark = '';
  var $gaps_pattern = '';
  var $matching_lines_limit = 0;
  var $gaps_radius = 25;

  function search_datasource_component()
  {
    $this->processor = new SearchResultProcessor();

    parent :: datasource_component();
  }

  function set_matching_lines_limit($limit)
  {
    $this->matching_lines_limit = $limit;
  }

  function set_left_mark($mark)
  {
    $this->left_mark = $mark;
  }

  function set_right_mark($mark)
  {
    $this->right_mark = $mark;
  }

  function set_gaps_pattern($pattern)
  {
    $this->gaps_pattern = $pattern;
  }

  function set_gaps_radius($radius)
  {
    $this->gaps_radius = $radius;
  }

  function & get_dataset()
  {
    $dataset =& parent :: get_dataset();

    $processed_dataset =& $this->_process($dataset);

    return $processed_dataset;
  }

  function _process(&$dataset)
  {
    if (!$query_object = $this->get_search_query_object())
      return new empty_dataset();

    $this->processor->setGapsPattern($this->gaps_pattern);
    $this->processor->setMatchedWordFoldingRadius($this->gaps_radius);
    $this->processor->setMatchingLinesLimit($this->matching_lines_limit);
    $this->processor->setMatchMarks($this->left_mark, $this->right_mark);

    $dataset->reset();

    $processed_data = array();
    if ($dataset->next())
    do
    {
      $row = &$dataset->export();
      $row['content'] = $this->processor->process($row['content'],
                                                  $query_object->get_query_items());
      $processed_data[] = $row;
    }while($dataset->next());

    return new array_dataset($processed_data);
  }

  function & get_search_query_object()
  {
    $datasource =& $this->_get_datasource();
    if (method_exists($datasource, 'get_search_query_object'))
      return $datasource->get_search_query_object();
    else
      error('Wrong datasource! Must support get_search_query_object() method',
            __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
            array('class' => get_class($datasource)));
  }
}

?>