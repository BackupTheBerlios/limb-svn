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
  public $tag = 'richedit';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'richedit_tag';
}

registerTag(new RicheditTagInfo());

class RicheditTag extends ControlTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/richedit_component';
  }

  public function getRenderedTag()
  {
    return 'textarea';
  }

  public function preGenerate($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->init_richedit();');

    parent :: preGenerate($code);
  }

  public function generateContents($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->render_contents();');
  }
}

?>