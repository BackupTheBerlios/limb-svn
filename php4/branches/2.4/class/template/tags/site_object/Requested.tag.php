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
class SiteObjectRequestedTagInfo
{
  public $tag = 'site_object:REQUESTED';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'site_object_requested_tag';
}

registerTag(new SiteObjectRequestedTagInfo());

class SiteObjectRequestedTag extends ServerComponentTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/site_object_component';
  }

  public function generateContents($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->fetch_requested();');

    parent :: generateContents($code);
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