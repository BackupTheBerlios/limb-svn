<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: version.tag.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
class limb_context_icon_tag_info
{
  var $tag = 'limb:CONTEXT_ICON';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'limb_context_icon_tag';
}

register_tag(new limb_context_icon_tag_info());

class limb_context_icon_tag extends server_component_tag 
{
  var $runtime_component_path = '/core/template/components/limb_context_icon_component';
  
  function generate_contents(&$code)
  {
    $ref = $this->get_component_ref_code();
    
    if(isset($this->attributes['resolve_by']) 
        && $this->attributes['resolve_by'] == 'path')
      $this->_set_path_resolver($code);
    else 
      $this->_set_identifier_resolver($code);

    if(isset($this->attributes['variation']))
      $code->write_php("{$ref}->set(\"variation\", \"". $this->attributes['variation'] ."\");\n");
      
    $code->write_php("{$ref}->get_icon();\n");

    parent :: generate_contents($code);
  }
  
  function _set_identifier_resolver(&$code)
  {
    $ref = $this->get_component_ref_code();
    if(!isset($this->attributes['identifier']))
      $code->write_php("{$ref}->set(\"identifier\", " . $this->parent->get_dataspace_ref_code() . "->get('identifier'));\n");
    else
      $code->write_php("{$ref}->set(\"identifier\", \"". $this->attributes['identifier'] ."\");\n");
    
    $code->write_php("{$ref}->resolve_by_identifier();\n");
  }

  function _set_path_resolver(&$code)
  {
    $ref = $this->get_component_ref_code();
    $code->write_php("{$ref}->set(\"path\", " . $this->parent->get_dataspace_ref_code() ."->get('path'));\n");

    if(!isset($this->attributes['level']))
      $this->attributes['level'] = '2';
    $code->write_php("{$ref}->set(\"level\", \"" . $this->attributes['level'] . "\");\n");

    $code->write_php("{$ref}->resolve_by_path();\n");
  }

}
?>