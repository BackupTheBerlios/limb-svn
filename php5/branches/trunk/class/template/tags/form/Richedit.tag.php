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
require_once(LIMB_DIR . '/class/template/tags/form/control_tag.class.php');

class richedit_tag_info
{
  public $tag = 'richedit';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'richedit_tag';
}

register_tag(new richedit_tag_info());

class richedit_tag extends control_tag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/richedit_component';
  }

  public function get_rendered_tag()
  {
    return 'textarea';
  }

  public function pre_generate($code)
  {
    $code->write_php($this->get_component_ref_code() . '->init_richedit();');

    parent :: pre_generate($code);
  }

  public function generate_contents($code)
  {
    $code->write_php($this->get_component_ref_code() . '->render_contents();');
  }
}

?>