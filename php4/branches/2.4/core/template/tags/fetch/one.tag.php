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


class fetch_one_tag_info
{
  var $tag = 'fetch:ONE';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'fetch_one_tag';
}

register_tag(new fetch_one_tag_info());

class fetch_one_tag extends server_component_tag
{
  var $runtime_component_path = '/core/template/components/fetch_component';

  function pre_generate(&$code)
  {
    parent :: pre_generate($code);
    $code->write_php($this->get_component_ref_code() . '->prepare();');
  }

  function generate_contents(&$code)
  {
    if(isset($this->attributes['path']))
    {
      $code->write_php($this->get_component_ref_code() . '->fetch("' . $this->attributes['path'] . '");');
    }
    else if(isset($this->attributes['hash_id']))
    {
      $path_tmp = '$' . $code->get_temp_variable();
      $uri_tmp = '$' . $code->get_temp_variable();

      $code->write_php(
        "{$path_tmp} = " . $this->parent->get_dataspace_ref_code() . '->get("' . $this->attributes['hash_id'] . '");');

      $code->register_include(LIMB_DIR . "/core/lib/http/uri.class.php");

      $code->write_php($uri_tmp . " = new uri(" . $path_tmp . ");" .
                       $this->get_component_ref_code() . "->fetch(" . $uri_tmp . "->get_path());");
    }

    parent :: generate_contents($code);
  }

  function post_generate(&$code)
  {
    parent :: post_generate($code);
  }

  function &get_dataspace()
  {
    return $this;
  }

  function get_dataspace_ref_code()
  {
    return $this->get_component_ref_code();
  }
}

?>