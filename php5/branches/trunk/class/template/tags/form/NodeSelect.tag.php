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

class NodeSelectTagInfo
{
  public $tag = 'node_select';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'node_select_tag';
}

registerTag(new NodeSelectTagInfo());

class NodeSelectTag extends ControlTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/node_select_component';
  }

  public function getRenderedTag()
  {
    return 'input';
  }

  public function preGenerate($code)
  {
    if(!isset($this->attributes['type']))
      $this->attributes['type'] = 'hidden';

    $code->writePhp($this->getComponentRefCode() . '->init_node_select();');

    parent :: preGenerate($code);
  }

  public function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->render_node_select();');
  }
}

?>