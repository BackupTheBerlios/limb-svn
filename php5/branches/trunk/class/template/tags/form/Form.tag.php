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
class FormTagInfo
{
  public $tag = 'form';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'form_tag';
}

registerTag(new FormTagInfo());

/**
* Compile time component for building runtime form_components
*/
class FormTag extends ServerTagComponentTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/form_component';
  }

  /**
  * Returns the identifying server ID. It's value it determined in the
  * following order;
  * <ol>
  * <li>The XML id attribute in the template if it exists</li>
  * <li>The XML name attribute in the template if it exists</li>
  * <li>The value of $this->server_id</li>
  * <li>An ID generated by the get_new_server_id() function</li>
  * </ol>
  */
  public function getServerId()
  {
    if (!empty($this->attributes['id']))
    {
      return $this->attributes['id'];
    }
    elseif (!empty($this->attributes['name']))
    {
      return $this->attributes['name'];
    }
    elseif (!empty($this->server_id))
    {
      return $this->server_id;
    }
    else
    {
      $this->server_id = getNewServerId();
      return $this->server_id;
    }
  }

  public function checkNestingLevel()
  {
    if ($this->findParentByClass('form_tag'))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    if (!isset($this->attributes['name']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'name',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function preGenerate($code)
  {
    parent :: preGenerate($code);

    $code->writePhp($this->getComponentRefCode() . '->preserve_state("submitted", 1);');
    $code->writePhp($this->getComponentRefCode() . '->render_state();');
  }

  public function generateContents($code)
  {
    parent :: generateContents($code);

    $v = '$' . $code->getTempVariable();

    $code->writePhp("if({$v} = Limb :: toolkit()->getRequest()->get('node_id')){");
    $code->writePhp("echo \"<input type='hidden' name='node_id' value='{$v}'>\";}");
  }

  public function getDataspace()
  {
    return $this;
  }

  public function getDataspaceRefCode()
  {
    return $this->getComponentRefCode();
  }
}

?>