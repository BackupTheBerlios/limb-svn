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

function toStudlyCaps($str, $ucfirst = true)
{
  $res = preg_replace('~([a-zA-Z])?_([a-zA-Z])~e',
                      "'\\1'.strtoupper('\\2')",
                      $str);

  return ($ucfirst) ? ucfirst($res) : $res;

}

function to_under_scores($str)
{
  return ltrim(preg_replace('~([a-z])?([A-Z])~e', "'\\1_'.strtolower('\\2')", $str),
               '_');
}

function addUrlQueryItems($url, $items=array())
{
  $str_params = '';

  $request = Limb :: toolkit()->getRequest();

  if (($node_id = $request->get('node_id')) &&  !isset($items['node_id']))
    $items['node_id'] = $node_id;

  if(strpos($url, '?') === false)
    $url .= '?';

  foreach($items as $key => $val)
  {
    $url = preg_replace("/&*{$key}=[^&]*/", '', $url);
    $str_params .= "&$key=$val";
  }

  $items = explode('#', $url);

  $url = $items[0];
  $fragment = isset($items[1]) ? '#' . $items[1] : '';

  return $url . $str_params . $fragment;
}


?>
