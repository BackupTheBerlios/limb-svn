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
class SiteObjectTagInfo
{
  var $tag = 'site_object';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'site_object';
}

registerTag(new SiteObjectTagInfo());

class SiteObjectTag extends ServerComponentTag
{
  function SiteObjectTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/site_object_component';
  }

  function preParse()
  {
    if (!isset($this->attributes['path']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'path',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function generateContents($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->fetch_by_path("' . $this->attributes['path'] . '");');

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