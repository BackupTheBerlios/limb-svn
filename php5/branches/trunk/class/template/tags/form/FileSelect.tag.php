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

class FileSelectTagInfo
{
  public $tag = 'file_select';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'file_select_tag';
}

registerTag(new FileSelectTagInfo());

class FileSelectTag extends ControlTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/file_select_component';
  }

  public function getRenderedTag()
  {
    return 'input';
  }

  public function preGenerate($code)
  {
    $this->attributes['type'] = 'hidden';

    $code->writePhp($this->getComponentRefCode() . '->init_file_select();');

    parent :: preGenerate($code);
  }

  public function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->render_file_select();');
  }
}

?>