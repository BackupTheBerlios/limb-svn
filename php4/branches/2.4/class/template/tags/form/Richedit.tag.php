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
require_once(LIMB_DIR . '/class/template/tags/form/ControlTag.class.php');

class RicheditTagInfo
{
  var $tag = 'richedit';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'richedit_tag';
}

registerTag(new RicheditTagInfo());

class RicheditTag extends ControlTag
{
  function RicheditTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/richedit_component';
  }

  function getRenderedTag()
  {
    return 'textarea';
  }

  function preGenerate($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->init_richedit();');

    parent :: preGenerate($code);
  }

  function generateContents($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->render_contents();');
  }
}

?>