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
class FormStatusTagInfo
{
  var $tag = 'form:STATUS';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'form_status_tag';
}

registerTag(new FormStatusTagInfo());

/**
* The parent compile time component for lists
*/
class FormStatusTag extends CompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if (!$this->findParentByClass('form_tag'))
    {
      return new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'form',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function generateContents($code)
  {
    $error_child = $this->findChildByClass('error_status_tag');
    $success_child = $this->findChildByClass('success_status_tag');

    $code->writePhp('if (!' . $this->getComponentRefCode() . '->is_first_time()) {');

    $code->writePhp('if (' . $this->getComponentRefCode() . '->is_valid()) {');
      if ($success_child)
        $success_child->generate($code);
    $code->writePhp('}else{');
      if ($error_child)
        $error_child->generate($code);
    $code->writePhp('}');

    $code->writePhp('}');
  }
}

?>