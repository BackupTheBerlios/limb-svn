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

class JsDesignVarsTagInfo
{
  public $tag = 'js:DESIGN_VARS';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'js_design_vars_tag';
}

registerTag(new JsDesignVarsTagInfo());

class JsDesignVarsTag extends CompilerDirectiveTag
{
  function generateContents($code)
  {
    $code->writeHtml("<script language='javascript'>\n");
    $code->writePhp('echo "var HTTP_SHARED_DIR = \'" . addslashes(fs :: clean_path(constant("HTTP_SHARED_DIR"))) . "\';";');
    $code->writePhp('echo "var LOCAL_DESIGN_DIR = \'" . addslashes(fs :: clean_path(constant("LIMB_APP_DIR") . "/design/")) . "\';";');
    $code->writeHtml("\n</script>");
  }
}

?>