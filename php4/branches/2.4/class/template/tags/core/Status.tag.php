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
  public $tag = 'core:STATUS';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'core_status_tag';
}

registerTag(new CoreStatusTagInfo());

/**
* Defines an action take, should a dataspace variable have been set at runtime.
* The opposite of the core_default_tag
*/
class CoreStatusTag extends CompilerDirectiveTag
{
  protected $const;

  public function preParse()
  {
    if (!isset($this->attributes['name']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'name',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    $this->const = $this->attributes['name'];

    return PARSER_REQUIRE_PARSING;
  }

  public function preGenerate($code)
  {
    $value = 'true';
    if (isset($this->attributes['value']) &&  !(boolean)$this->attributes['value'])
      $value = 'false';

    $tempvar = $code->getTempVariable();
    $code->writePhp('$' . $tempvar . ' = trim(' . $this->getDataspaceRefCode() . '->get("status"));');
    $code->writePhp('if ((boolean)(constant("' . $this->const . '") & $' . $tempvar . ') === ' . $value . ') {');

    parent::preGenerate($code);
  }

  public function postGenerate($code)
  {
    parent::postGenerate($code);

    $code->writePhp('}');
  }
}

?>