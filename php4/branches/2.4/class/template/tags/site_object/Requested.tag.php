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
  var $tag = 'site_object:REQUESTED';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'site_object_requested_tag';
}

registerTag(new SiteObjectRequestedTagInfo());

class SiteObjectRequestedTag extends ServerComponentTag
{
  function SiteObjectRequestedTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/site_object_component';
  }

  function generateContents($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->fetch_requested();');

    parent :: generateContents($code);
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