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
  var $tag = 'core:DATASPACE';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'core_dataspace_tag';
}

registerTag(new CoreDataspaceTagInfo());

/**
* Dataspaces act is "namespaces" for a template.
*/
class CoreDataspaceTag extends ServerComponentTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/dataspace_component';
  }

  function preGenerate($code)
  {
    parent :: preGenerate($code);

    $code->writePhp('if (!' . $this->getDataspaceRefCode() . '->is_empty()){');
  }

  function postGenerate($code)
  {
    $code->writePhp('}');

    parent :: postGenerate($code);
  }

  function getDataspace()
  {
    return $this;
  }

  function getDataspaceRefCode()
  {
    return $this->getComponentRefCode();
  }
}

?>