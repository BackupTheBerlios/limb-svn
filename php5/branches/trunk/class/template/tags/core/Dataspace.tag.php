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
class CoreDataspaceTagInfo
{
  public $tag = 'core:DATASPACE';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'core_dataspace_tag';
}

registerTag(new CoreDataspaceTagInfo());

/**
* Dataspaces act is "namespaces" for a template.
*/
class CoreDataspaceTag extends ServerComponentTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/dataspace_component';
  }

  public function preGenerate($code)
  {
    parent :: preGenerate($code);

    $code->writePhp('if (!' . $this->getDataspaceRefCode() . '->is_empty()){');
  }

  public function postGenerate($code)
  {
    $code->writePhp('}');

    parent :: postGenerate($code);
  }

  public function getDataspace()
  {
    return $this;
  }

  public function getDataspaceRefCode()
  {
    return $this->getComponentRefCode();
  }
}

?>