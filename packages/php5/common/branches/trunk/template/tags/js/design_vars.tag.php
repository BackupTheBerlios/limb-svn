<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class js_design_vars_tag_info
{
	public $tag = 'js:DESIGN_VARS';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'js_design_vars_tag';
} 

register_tag(new js_design_vars_tag_info());

class js_design_vars_tag extends compiler_directive_tag
{	
	public function generate_contents($code)
	{
    $code->write_html("<script language='javascript'>\n");	  				
	  $code->write_php('echo "var HTTP_SHARED_DIR = \'" . addslashes(fs :: clean_path(constant("HTTP_SHARED_DIR"))) . "\';";');
	  $code->write_php('echo "var LOCAL_DESIGN_DIR = \'" . addslashes(fs :: clean_path(constant("LIMB_APP_DIR") . "/design/")) . "\';";');
	  $code->write_html("\n</script>");
	}
} 

?>