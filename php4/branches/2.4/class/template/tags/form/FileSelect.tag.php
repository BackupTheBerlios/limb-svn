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
  var $tag = 'file_select';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'file_select_tag';
}

registerTag(new FileSelectTagInfo());

class FileSelectTag extends ControlTag
{
  function FileSelectTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/file_select_component';
  }

  function getRenderedTag()
  {
    return 'input';
  }

  function preGenerate($code)
  {
    $this->attributes['type'] = 'hidden';

    $code->writePhp($this->getComponentRefCode() . '->init_file_select();');

    parent :: preGenerate($code);
  }

  function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->render_file_select();');
  }
}

?>