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
  public $tag = 'site_object';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'site_object';
}

registerTag(new SiteObjectTagInfo());

class SiteObjectTag extends ServerComponentTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/site_object_component';
  }

  public function preParse()
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

  public function generateContents($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->fetch_by_path("' . $this->attributes['path'] . '");');

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