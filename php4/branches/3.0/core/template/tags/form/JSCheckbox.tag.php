<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: js_checkbox.tag.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once WACT_ROOT . 'template/tags/form/control.inc.php';

$taginfo =& new TagInfo('js_checkbox', 'JSCheckboxTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
$taginfo->setEndTag(ENDTAG_FORBIDDEN);
$taginfo->setCompilerAttributes(array('errorclass', 'errorstyle', 'displayname'));
$taginfo->setKnownParent('FormTag');
TagDictionary::registerTag($taginfo, __FILE__);

class JSCheckboxTag extends ControlTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%/core/template/components/form/JSCheckboxComponent.class.php';
  var $runtimeComponentName = 'JSCheckboxComponent';

  function prepare()
  {
    $this->setAttribute('type', 'hidden');

    parent :: prepare();
  }

  function getRenderedTag()
  {
    return 'input';
  }

  function generateContents(&$code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->renderJSCheckbox();');
  }
}

?>