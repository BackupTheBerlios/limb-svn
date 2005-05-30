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
require_once(LIMB_DIR . '/core/template/tags/datasource/datasource.tag.php');

class actions_tag_info
{
  var $tag = 'actions';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'actions_tag';
}

register_tag(new actions_tag_info());

class actions_tag extends datasource_tag
{
  var $runtime_component_path = '/core/template/components/actions_component';
}
?>