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
class CoreStatusTagInfo
{
  var $tag = 'core:STATUS';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'core_status_tag';
}

registerTag(new CoreStatusTagInfo());

/**
* Defines an action take, should a dataspace variable have been set at runtime.
* The opposite of the core_default_tag
*/
class CoreStatusTag extends CompilerDirectiveTag
{
  var $const;

  function preParse()
  {
    if (!isset($this->attributes['name']))
    {
      return new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'name',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    $this->const = $this->attributes['name'];

    return PARSER_REQUIRE_PARSING;
  }

  function preGenerate($code)
  {
    $value = 'true';
    if (isset($this->attributes['value']) &&  !(boolean)$this->attributes['value'])
      $value = 'false';

    $tempvar = $code->getTempVariable();
    $code->writePhp('$' . $tempvar . ' = trim(' . $this->getDataspaceRefCode() . '->get("status"));');
    $code->writePhp('if ((boolean)(constant("' . $this->const . '") & $' . $tempvar . ') === ' . $value . ') {');

    parent::preGenerate($code);
  }

  function postGenerate($code)
  {
    parent::postGenerate($code);

    $code->writePhp('}');
  }
}

?>