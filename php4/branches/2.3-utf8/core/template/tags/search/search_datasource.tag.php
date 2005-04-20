<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
class search_datasource_tag_info
{
  var $tag = 'search:DATASOURCE';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'search_datasource_tag';
}

register_tag(new search_datasource_tag_info());

class search_datasource_tag extends datasource_tag
{
  var $runtime_component_path = '/core/template/components/search_datasource_component';

  function pre_generate(&$code)
  {
    parent::pre_generate($code);

    if(isset($this->attributes['lines_limit']))
    {
      $code->write_php($this->get_component_ref_code() . '->set_matching_lines_limit("' . $this->attributes['lines_limit'] .'");');
    }

    if(isset($this->attributes['gaps_radius']))
    {
      $code->write_php($this->get_component_ref_code() . '->set_gaps_radius("' . $this->attributes['gaps_radius'] .'");');
    }
  }
}

?>