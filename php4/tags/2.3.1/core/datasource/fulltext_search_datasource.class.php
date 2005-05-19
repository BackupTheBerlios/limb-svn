<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');
require_once(LIMB_DIR . '/core/model/search/search_query.class.php');
require_once(LIMB_DIR . '/core/model/search/normalizers/search_text_normalizer.class.php');
require_once(SEARCH_SPIDER_DIR . '/FullTextSearcher.class.php');

class fulltext_search_datasource extends datasource
{
  var $query_object;

  function & get_dataset(&$counter, $params)
  {
    if(!$query_object = $this->get_search_query_object())
      return new empty_dataset();

    $searcher = new FullTextSearcher();

    if (isset($params['limit']))
      $limit = $params['limit'];
    else
      $limit = 0;

    if (isset($params['offset']))
      $offset = $params['offset'];
    else
      $offset = 0;

    $result = $searcher->find($query_object, $limit, $offset);
    $counter = $searcher->count($query_object);

    return new array_dataset($result);
  }

  function get_search_query_object()
  {
    if($this->query_object)
      return $this->query_object;

    $request = request :: instance();

    if (!$search_query = trim($request->get_attribute('search_query')))
      return null;

    $this->query_object = new search_query();

    $search_query = search_text_normalizer :: process($search_query);
    $words = explode(' ', $search_query);

    foreach($words as $word)
      $this->query_object->add($word);

    return $this->query_object;
  }
}
?>