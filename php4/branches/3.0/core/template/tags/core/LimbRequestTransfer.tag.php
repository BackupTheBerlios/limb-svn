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

$taginfo =& new TagInfo('limb:REQUEST_TRANSFER', 'LimbRequestTransferTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbRequestTransferTag extends ServerTagComponentTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%core/template/components/LimbRequestTransferComponent.class.php';
  var $runtimeComponentName = 'LimbRequestTransferComponent';

  function preParse()
  {
    $attr = $this->getAttribute('attributes');
    if (empty($attr))
    {
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'attributes'));
    }

    return PARSER_REQUIRE_PARSING;
  }

  function preGenerate($code)
  {
    //we override parent behavior
  }

  function postGenerate($code)
  {
    //we override parent behavior
  }

  function generateContents(&$code)
  {
    $content = $code->getTempVarRef();
    $attributes = $code->getTempVarRef();

    $code->writePhp('ob_start();');

    parent :: generateContents($code);

    $code->writePhp("{$attributes} = explode(',', '" . $this->getAttribute('attributes') . "');");
    $code->writePhp("{$content} = ob_get_contents();ob_end_clean();");

    $code->writePhp($this->getComponentRefCode() . "->setAttributesToTransfer({$attributes});");
    $code->writePhp($this->getComponentRefCode() . "->appendRequestAttributes({$content});");

    $code->writePhp("echo {$content};");
  }
}

?>