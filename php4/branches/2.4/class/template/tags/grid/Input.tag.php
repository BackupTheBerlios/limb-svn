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
require_once(LIMB_DIR . '/class/template/tags/form/ControlTag.class.php');

class GridInputTagInfo
{
  var $tag = 'grid:INPUT';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'grid_input_tag';
}

registerTag(new GridInputTagInfo());

class GridInputTag extends ControlTag
{
  function GridInputTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/grid_input_component';
  }

  function checkNestingLevel()
  {
    if (!$this->findParentByClass('grid_iterator_tag'))
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'grid:ITERATOR',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function preParse()
  {
    if (!isset($this->attributes['name']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'name',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function prepare()
  {
    $this->attributes['type'] = 'text';

    $grid_tag = $this->findParentByClass('grid_list_tag');
    $grid_tag->setFormRequired();

    parent :: prepare();
  }

  function getRenderedTag()
  {
    return 'input';
  }
}

?>