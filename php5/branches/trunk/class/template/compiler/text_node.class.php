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
/**
* Used to write literal text from the source template to the compiled
* template
*/
class text_node extends compiler_directive_tag
{
  /**
  * A text string to write
  */
  protected $contents;

  /**
  * Constructs text_node
  */
  public function text_node($text)
  {
    $this->contents = $text;
  }

  /**
  * Writes the contents of the text node to the compiled template
  * using the write_html method
  */
  public function generate($code)
  {
    $code->write_html($this->contents);
  }
}

?>