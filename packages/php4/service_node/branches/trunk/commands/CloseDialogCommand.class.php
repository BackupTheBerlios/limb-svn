<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: RedirectCommand.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/RedirectCommand.class.php');

define('RELOAD_SELF_URL', '');

class CloseDialogCommand
{
  function CloseDialogCommand(&$service_node)
  {
    $this->service_node =& $service_node;
  }

  function perform()
  {
    if(!is_a($this->service_node, 'ServiceNode'))
      return LIMB_STATUS_ERROR;

    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();

    $node =& $this->service_node->getNodePart();
    $path2id_translator =& $toolkit->getPath2IdTranslator();

    if($uow->isDeleted($this->service_node))
    {
      $path = $path2id_translator->getPathToNode($node->get('parent_id')) . '?action=admin_display';
    }
    else
      $path = '';

    $result_str = $this->close_popup_response(&$request, $path);

    $toolkit =& Limb :: toolkit();
    $response =& $toolkit->getResponse();
    return $response->write($result_str);
  }

  function close_popup_response(&$request, $parent_reload_url = RELOAD_SELF_URL)
  {
    $str = "<html><body><script>
                if(window.opener)
                {";

    if($parent_reload_url != RELOAD_SELF_URL)
      $str .=			"	href = '{$parent_reload_url}';";
    else
      $str .=			"	href = window.opener.location.href;";

    $str .=				$this->_add_js_random_to_url('href');

    $str .=				"	window.opener.location.href = href;";

    $str .=				" window.opener.focus();
                  }
                  window.close();
                </script></body></html>";

    return $str;

  }

  function _add_js_random_to_url($href)
  {
    return $this->_add_js_param_to_url($href, 'rn', 'Math.floor(Math.random()*10000)');
  }

  function _add_js_param_to_url($href, $param, $value)
  {
    return "
      if({$href}.indexOf('?') == -1)
        {$href} = {$href} + '?';

      {$href} = {$href}.replace(/&*rn=[^&]+/g, '');

      items = {$href}.split('#');

      {$href} = items[0] + '&{$param}=' + {$value};

      if(items[1])
        {$href} = {$href} + '#' + items[1];";

  }
}


?>
