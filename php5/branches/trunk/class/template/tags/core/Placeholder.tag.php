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
class CorePlaceHolderTagInfo
{
  public $tag = 'core:PLACEHOLDER';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'core_place_holder_tag';
}

registerTag(new CorePlaceHolderTagInfo());

/**
* Present a named location where content can be inserted at runtime
*/
class CorePlaceHolderTag extends ServerComponentTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/placeholder_component';
  }

  public function checkNestingLevel()
  {
    if ($this->findParentByClass('core_place_holder_tag'))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }
}

?>