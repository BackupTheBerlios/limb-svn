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
require_once(WACT_ROOT . '/template/template.inc.php');

class LimbRequestTransferComponent extends Component
{
  var $attributes = array();
  var $attributes_string = '';//used in replace callback

  function setAttributesToTransfer($attributes)
  {
    $this->attributes = $attributes;
  }

  function appendRequestAttributes(&$content)
  {
    $attributes_to_append = array();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    foreach($this->attributes as $attribute)
    {
      if($value = $request->get($attribute))
        $attributes_to_append[] = $attribute . '=' . addslashes($value);
    }
    if($this->attributes_string = implode('&', $attributes_to_append))
    {
      $callback = array(&$this,'_replaceCallback');
      $content = preg_replace_callback("/(<(?:a|area|form|frame|input)[^>\\w]+(?:href|action|src)=)(?>(\"|'))?((?(2)[^\\2>]+?|[^\\s>]+))((?(2)\\2)[^>]*>)/",
                                       $callback,
                                       $content);
    }
  }

  function _replaceCallback($matches)
  {
    $matches[3] = rtrim($matches[3], '?') . '?&' . $this->attributes_string;

    return $matches[1] . $matches[2] . $matches[3] . $matches[4];
  }
}

?>