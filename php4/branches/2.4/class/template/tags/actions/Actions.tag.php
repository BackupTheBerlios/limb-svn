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
class ActionsTagInfo
{
  var $tag = 'actions';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'actions_tag';
}

registerTag(new ActionsTagInfo());

class ActionsTag extends ServerComponentTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/actions_component';
  }

  function preGenerate($code)
  {
    parent :: preGenerate($code);

    $actions_array = '$' . $code->getTempVariable();
    $node_id = '$' . $code->getTempVariable();
    $node = '$' . $code->getTempVariable();
    $code->writePhp("{$actions_array} = ".  $this->parent->getDataspaceRefCode() . '->get("actions");'."\n");

    $code->writePhp("{$node_id} = " . $this->parent->getDataspaceRefCode() . '->get("node_id");'. "\n");

    $code->writePhp("if(!{$node_id}){
      {$node} = Limb :: toolkit()->getFetcher()->mapRequestToNode(Limb :: toolkit()->getRequest()); {$node_id} = {$node}['id'];}\n");

    $code->writePhp($this->getComponentRefCode() . "->setActions({$actions_array});\n");

    $code->writePhp($this->getComponentRefCode() . "->setNodeId({$node_id});\n");

    $code->writePhp($this->getComponentRefCode() . '->prepare();'."\n");

    $code->writePhp('if (' . $this->getComponentRefCode() . '->next()) {');
  }

  function postGenerate($code)
  {
    $code->writePhp('}');
  }

  function getDataspace()
  {
    return $this;
  }

  function getDataspaceRefCode()
  {
    return $this->getComponentRefCode() . '->dataset';
  }
}

?>