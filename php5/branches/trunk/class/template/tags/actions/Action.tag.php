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
class ActionTagInfo
{
  public $tag = 'actions:ITEM';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'action_tag';
}

registerTag(new ActionTagInfo());

/**
* Compile time component for items (rows) in the list
*/
class ActionTag extends CompilerDirectiveTag
{
  public function checkNestingLevel()
  {
    if (!$this->parent instanceof ActionsTag)
    {
      throw new WactException('wrong parent tag',
          array('tag' => $this->tag,
          'parent_class' => get_class($this->parent),
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function generateContents($code)
  {
    $code->writePhp('do { ');

    parent::generateContents($code);

    $code->writePhp('} while (' . $this->getDataspaceRefCode() . '->next());');
  }
}

?>