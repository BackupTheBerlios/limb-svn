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
class GridIteratorTagInfo
{
  public $tag = 'grid:ITERATOR';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'grid_iterator_tag';
}

registerTag(new GridIteratorTagInfo());

class GridIteratorTag extends CompilerDirectiveTag
{
  public function checkNestingLevel()
  {
    if (!$this->parent instanceof GridListTag)
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'grid:LIST',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function preGenerate($code)
  {
    parent::preGenerate($code);

    $code->writePhp('if (' . $this->getComponentRefCode() . '->next()) {');
  }

  public function generateContents($code)
  {
    $code->writePhp('do { ');

    parent :: generateContents($code);

    $code->writePhp('} while (' . $this->getDataspaceRefCode() . '->next());');
  }

  public function postGenerate($code)
  {
    $code->writePhp('}');

    parent::postGenerate($code);
  }
}

?>