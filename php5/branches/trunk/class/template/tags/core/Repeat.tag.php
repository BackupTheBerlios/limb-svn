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
class CoreDataRepeatTagInfo
{
  public $tag = 'core:REPEAT';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'core_data_repeat_tag';
}

registerTag(new CoreDataRepeatTagInfo());

class CoreDataRepeatTag extends CompilerDirectiveTag
{
  public function generateContents($code)
  {
    $dataspace = $this->getDataspaceRefCode();

    $counter = '$' . $code->getTempVariable();
    $value = '$' . $code->getTempVariable();

    if (isset($this->attributes['hash_id']))
    {
      $code->writePhp($value . ' = trim(' . $this->getDataspaceRefCode() . '->get(\'' . $this->attributes['hash_id'] . '\'));');
    }
    else
    {
      if(!isset($this->attributes['value']))
        $this->attributes['value'] = 1;

      $code->writePhp($value . ' = ' . $this->attributes['value'] . ';');
    }

    $code->writePhp('for(' . $counter . '=0;' . $counter . ' < ' . $value . '; ' . $counter . '++){');

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>