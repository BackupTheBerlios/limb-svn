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
class GridStripeTagInfo
{
  public $tag = 'grid:STRIPE';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'grid_stripe_tag';
}

registerTag(new GridStripeTagInfo());

class GridStripeTag extends CompilerDirectiveTag
{
  public function checkNestingLevel()
  {
    if ($this->findParentByClass('grid_stripe_tag'))
    {
      throw new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    if (!$this->parent instanceof GridIteratorTag)
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'grid:ITERATOR',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  public function generateContents($code)
  {
    if (array_key_exists('even', $this->attributes))
    {
      $code->writePhp('if (!(' . $this->getDataspaceRefCode() . '->get_counter()%2)) {');
      parent :: generateContents($code);
      $code->writePhp('}');
    }
    elseif (array_key_exists('odd', $this->attributes))
    {
      $code->writePhp('if ((' . $this->getDataspaceRefCode() . '->get_counter()%2)) {');
      parent :: generateContents($code);
      $code->writePhp('}');
    }
  }
}

?>