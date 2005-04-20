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
require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');
if(!defined('DEFAULT_RICHEDIT'))
  @define('DEFAULT_RICHEDIT' , 'htmlarea');

class richedit_tag_info
{
  var $tag = 'richedit';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'richedit_tag';
}

register_tag(new richedit_tag_info());

class richedit_tag extends control_tag
{
  function prepare()
  {
    switch(DEFAULT_RICHEDIT)
    {
      case 'htmlarea':
        $this->runtime_component_path = '/core/template/components/form/richedit/htmlarea_component';
      break;
      case 'fckeditor':
        $this->runtime_component_path = '/core/template/components/form/richedit/fckeditor_component';
      break;
      case 'wysiwygpro':
        $this->runtime_component_path = '/core/template/components/form/richedit/wysiwygpro_component';
      break;
      default:
        $this->runtime_component_path = '/core/template/components/form/richedit/richedit_component';
      break;
    }

    parent :: prepare();
  }

  function pre_generate(&$code)
  {
  }

  function generate_contents(&$code)
  {
    $code->write_php($this->get_component_ref_code() . '->init_richedit();');
    $code->write_php($this->get_component_ref_code() . '->render_contents();');
  }

  function post_generate(&$code)
  {
  }
}
?>