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
class action_icon_tag_info
{
  var $tag = 'action:ICON';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'action_icon_tag';
}
register_tag(new action_icon_tag_info());

class action_icon_tag extends server_component_tag
{
  var $runtime_component_path = '/core/template/components/action_icon_component';

  function generate_contents(&$code)
  {
    $ref = $this->get_component_ref_code();

    if(isset($this->attributes['identifier']))
      $code->write_php("{$ref}->set(\"identifier\", \"". $this->attributes['id'] ."\");\n");
    else
      $code->write_php("{$ref}->set(\"identifier\", " . $this->parent->get_dataspace_ref_code() ."->get('icon'));\n");

    if(isset($this->attributes['variation']))
      $code->write_php("{$ref}->set(\"variation\", \"". $this->attributes['variation'] ."\");\n");

    $code->write_php("{$ref}->get_icon_path();\n");

    parent :: generate_contents($code);
  }

}
?>