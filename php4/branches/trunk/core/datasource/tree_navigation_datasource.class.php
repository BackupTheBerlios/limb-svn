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
require_once(LIMB_DIR . '/core/datasource/fetch_tree_datasource.class.php');
require_once(LIMB_DIR . '/core/lib/http/uri.class.php');

class tree_navigation_datasource extends fetch_tree_datasource
{
	function & _fetch(&$counter, $params)
	{
		$result =& parent :: _fetch($counter, $params);
		$requested_uri = new uri($_SERVER['REQUEST_URI']);
    $nav_uri = new uri();		
 
		foreach($result as $key => $data)
		{ 
      $nav_uri->parse($data['url']);
			if($requested_uri->compare_path($nav_uri) === 0 )
			{ 
        $result[$key]['selected'] = true;
        
        if($nav_uri->get_query_item('action') !== $requested_uri->get_query_item('action'))
        {        
          $result[$key]['selected'] = false;
        }
			}
		}
		return $result;
	}

}


?>