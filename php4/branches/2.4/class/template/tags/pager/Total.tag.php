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
class PagerTotalCountTagInfo
{
  var $tag = 'pager:TOTAL';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'pager_total_count_tag';
}

registerTag(new PagerTotalCountTagInfo());

/**
* Compile time component for seperators in a Pager
*/
class PagerTotalCountTag extends ServerComponentTag
{
  function checkNestingLevel()
  {
    if (!$this->findParentByClass('pager_navigator_tag'))
    {
      throw new WactException('missing enclosure',
          array('tag' => $this->tag,
          'enclosing_tag' => 'pager:navigator',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function preGenerate($code)
  {
    $parent = $this->findParentByClass('pager_navigator_tag');
    parent::preGenerate($code);

    $code->writePhp($this->getComponentRefCode() . '->set("number", ' . $parent->getComponentRefCode() . '->get_total_items());');
    $code->writePhp($this->getComponentRefCode() . '->set("pages_count", ' . $parent->getComponentRefCode() . '->get_pages_count());');
    $code->writePhp($this->getComponentRefCode() . '->set("more_than_one_page", ' . $parent->getComponentRefCode() . '->has_more_than_one_page());');
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