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
class CoreOptionalTagInfo
{
  var $tag = 'core:OPTIONAL';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'core_optional_tag';
}

registerTag(new CoreOptionalTagInfo());

/**
* Defines an action take, should a dataspace variable have been set at runtime.
* The opposite of the core_default_tag
*/
class CoreOptionalTag extends CompilerDirectiveTag
{
  function preParse()
  {
    if (!isset($this->attributes['for']) ||  !$this->attributes['for'])
    {
      return throw(new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'for',
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function preGenerate($code)
  {
    parent::preGenerate($code);

    $tempvar = $code->getTempVariable();
    $code->writePhp('$' . $tempvar . ' = trim(' . $this->getDataspaceRefCode() . '->get(\'' . $this->attributes['for'] . '\'));');
    $code->writePhp('if (!empty($' . $tempvar . ')) {');
  }

  function postGenerate($code)
  {
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>