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
class MetadataTitleTagInfo
{
  public $tag = 'METADATA:TITLE';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'metadata_title_tag';
}

registerTag(new MetadataTitleTagInfo());

class MetadataTitleTag extends ServerComponentTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../components/metadata_component';
  }

  public function generateContents($code)
  {
    $ref = $this->getComponentRefCode();

    if(isset($this->attributes['separator']))
    {
      $code->writePhp("{$ref}->setTitleSeparator(\"". $this->attributes['separator'] ."\");\n");
    }

    if(isset($this->attributes['offset_path']))
      $code->writePhp($this->getComponentRefCode() . '->set_offset_path("' . $this->attributes['offset_path'] . '");');

    $ref = $this->getComponentRefCode();
    $code->writePhp("echo {$ref}->get_title();\n");

    parent :: generateContents($code);
  }
}

?>