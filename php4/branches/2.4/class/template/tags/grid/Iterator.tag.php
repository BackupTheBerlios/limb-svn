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
  var $tag = 'grid:ITERATOR';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'grid_iterator_tag';
}

registerTag(new GridIteratorTagInfo());

class GridIteratorTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
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

  function preGenerate($code)
  {
    parent::preGenerate($code);

    $code->writePhp('if (' . $this->getComponentRefCode() . '->next()) {');
  }

  function generateContents($code)
  {
    $code->writePhp('do { ');

    parent :: generateContents($code);

    $code->writePhp('} while (' . $this->getDataspaceRefCode() . '->next());');
  }

  function postGenerate($code)
  {
    $code->writePhp('}');

    parent::postGenerate($code);
  }
}

?>