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
class MetadataMetadataTagInfo
{
  public $tag = 'METADATA:METADATA';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'metadata_metadata_tag';
}

registerTag(new MetadataMetadataTagInfo());

class MetadataMetadataTag extends ServerComponentTag
{
  function MetadataMetadataTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../components/metadata_component';
  }

  function generateContents($code)
  {
    $ref = $this->getComponentRefCode();
    $code->writePhp("{$ref}->load_metadata();\n");

    parent :: generateContents($code);
  }

  function getDataspace()
  {
    return $this;
  }

  function getDataspaceRefCode()
  {
    return $this->getComponentRefCode();
  }
}

?>