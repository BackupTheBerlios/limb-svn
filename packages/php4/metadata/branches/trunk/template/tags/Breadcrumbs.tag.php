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
class MetadataBreadcrumbsTagInfo
{
  public $tag = 'metadata:BREADCRUMBS';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'metadata_breadcrumbs_tag';
}

registerTag(new MetadataBreadcrumbsTagInfo());

class MetadataBreadcrumbsTag extends ServerComponentTag
{
  function MetadataBreadcrumbsTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../components/metadata_component';
  }

  function generateContents($code)
  {
    $child_list = $this->findImmediateChildByClass('grid_list_tag');

    if(isset($this->attributes['request_path_attribute']))
      $code->writePhp($this->getComponentRefCode() . '->set_request_path("' . $this->attributes['request_path_attribute']. '");');

    if(isset($this->attributes['offset_path']))
      $code->writePhp($this->getComponentRefCode() . '->set_offset_path("' . $this->attributes['offset_path'] . '");');

    if ($child_list)
      $code->writePhp($child_list->getComponentRefCode() . '->register_dataset(' . $this->getComponentRefCode() . '->get_breadcrumbs_dataset());');

    parent :: generateContents($code);
  }
}

?>