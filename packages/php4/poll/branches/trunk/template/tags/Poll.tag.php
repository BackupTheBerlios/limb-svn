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
class PollTagInfo
{
  public $tag = 'poll';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'poll_tag';
}

registerTag(new PollTagInfo());

/**
* The parent compile time component for lists
*/
class PollTag extends ServerComponentTag
{
  function PollTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../components/poll_component';
  }

  function preGenerate($code)
  {
    parent::preGenerate($code);

    $code->writePhp($this->getComponentRefCode() . '->prepare();');
  }

  function generateContents($code)
  {
    $form_child = $this->findChildByClass('poll_form_tag');
    $results_child = $this->findChildByClass('poll_result_tag');

    $code->writePhp('if (' . $this->getComponentRefCode() . '->poll_exists()) {');

    $code->writePhp('if (' . $this->getComponentRefCode() . '->can_vote()) {');

    if($form_child)
      $form_child->generate($code);

      $code->writePhp('}else{');

    if ($results_child)
      $results_child->generate($code);

    $code->writePhp('}}');
  }

  function getDataspace()
  {
    return $this;
  }

  function getDataspaceRefCode()
  {
    return $this->getComponentRefCode();
  }
}

?>