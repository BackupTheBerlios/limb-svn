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
class FormErrorsTagInfo
{
  public $tag = 'form:ERRORS';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'form_errors_tag';
}

registerTag(new FormErrorsTagInfo());

class FormErrorsTag extends ServerComponentTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/list_component';
  }

  public function checkNestingLevel()
  {
    if (!$this->findParentByClass('form_tag'))
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'form',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function preParse()
  {
    if (!isset($this->attributes['target']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'target',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_REQUIRE_PARSING;
  }


  public function generateContents($code)
  {
    $parent_form = $this->findParentByClass('form_tag');

    $target = $this->parent->findChild($this->attributes['target']);

    $code->writePhp($target->getComponentRefCode() . '->register_dataset(' .
      $parent_form->getComponentRefCode() . '->get_error_dataset());');

    parent :: generateContents($code);
  }
}

?>