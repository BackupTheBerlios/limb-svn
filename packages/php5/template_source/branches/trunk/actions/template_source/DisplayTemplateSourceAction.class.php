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
require_once(LIMB_DIR . '/class/core/actions/Action.class.php');
require_once(LIMB_DIR . '/class/template/fileschemes/compiler_support.inc.php');

if (!defined('TEMPLATE_FOR_HACKERS'))
  define('TEMPLATE_FOR_HACKERS', '/template_source/for-hackers.html');

class DisplayTemplateSourceAction extends Action
{
  protected $history = array();

  public function perform($request, $response)
  {
    if(($t = $request->get('t')) &&  is_array($t) &&  sizeof($t) > 0)
    {
      $this->history = $t;
      $template_path = end($this->history);
    }
    else
      $template_path = TEMPLATE_FOR_HACKERS;

    if(substr($template_path, -5,  5) != '.html')
      $template_path = TEMPLATE_FOR_HACKERS;

    if(substr($template_path, -5,  5) != '.html')
      $request->setStatus(Request :: STATUS_FAILURE);

    try
    {
      $source_file_path = resolveTemplateSourceFileName($template_path);
    }
    catch(LimbException $e)
    {
      try
      {
        $source_file_path = resolveTemplateSourceFileName(TEMPLATE_FOR_HACKERS);
      }
      catch(LimbException $final_e)
      {
        $request->setStatus(Request :: STATUS_FAILURE);
        return;
      }
    }

    $template_contents = file_get_contents($source_file_path);

    if(sizeof($this->history) > 1)
    {
      $tmp_history = $this->history;

      $from_template_path = $tmp_history[sizeof($tmp_history) - 2];
      $tmp_history = array_splice($tmp_history, 0, sizeof($tmp_history) - 1);

      $history_query = 't[]=' . implode('&t[]=', $tmp_history);

      $this->view->set('history_query', $history_query);
      $this->view->set('from_template_path', $from_template_path);
    }

    $this->view->set('template_path', $template_path);
    $this->view->set('template_content', $this->_processTemplateContent($template_contents));
  }

  protected function _getTemplatePathFromNode($node_id)
  {
    $datasource = Limb :: toolkit()->getDatasource('RequestedObjectDatasource');
    $datasource->setNodeId($node_id);

    if(!$site_object = wrapWithSiteObject($datasource->fetch()))
      return null;

    $controller = $site_object->getController();

    return $controller->getActionProperty($controller->getDefaultAction(), 'template_path');
  }

  protected function _processTemplateContent($template_contents)
  {
    include_once(LIMB_DIR . '/class/template/compiler/template_compiler.inc.php');
    include_once(dirname(__FILE__) . '/../../TemplateHighlightHandler.class.php');
    include_once(LIMB_COMMON_DIR . '/setup_HTMLSax.inc.php');

    global $tag_dictionary; //fixx

    $parser = new XMLHTMLSax3();

    $handler = new TemplateHighlightHandler($tag_dictionary);

    $handler->setTemplatePathHistory($this->history);

    $parser->setObject($handler);

    $parser->setElementHandler('open_handler','close_handler');
    $parser->setDataHandler('data_handler');
    $parser->setEscapeHandler('escape_handler');

    $parser->parse($template_contents);

    $html = $handler->getHtml();

    return $html;
  }
}

?>