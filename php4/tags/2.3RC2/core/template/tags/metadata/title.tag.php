<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class metadata_title_tag_info
{
  var $tag = 'METADATA:TITLE';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'metadata_title_tag';
}

register_tag(new metadata_title_tag_info());

class metadata_title_tag extends server_component_tag
{
  var $runtime_component_path = '/core/template/components/metadata_component';

  function generate_contents(&$code)
  {
    $ref = $this->get_component_ref_code();

    if(isset($this->attributes['separator']))
    {
      $code->write_php("{$ref}->set_title_separator(\"". $this->attributes['separator'] ."\");\n");
    }

    if(isset($this->attributes['offset_path']))
      $code->write_php($this->get_component_ref_code() . '->set_offset_path("' . $this->attributes['offset_path'] . '");');

    $ref = $this->get_component_ref_code();
    $code->write_php("echo {$ref}->get_title();\n");

    parent :: generate_contents($code);

  }
}

?>