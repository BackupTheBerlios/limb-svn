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
$taginfo =& new TagInfo('limb:locale:STRING', 'LimbLocaleStringTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbLocaleStringTag extends CompilerDirectiveTag
{
  function CheckNestingLevel()
  {
    if ($this->findParentByClass('LimbLocaleStringTag'))
      $this->raiseCompilerError('BADSELFNESTING');
  }

  function preGenerate(&$code)
  {
    $code->writePhp('ob_start();');
  }

  function postGenerate(&$code)
  {
    $code->registerInclude(LIMB_DIR . '/core/i18n/Strings.class.php');

    $file = 'common';

    if(!$file = $this->getAttribute('file'))
      $file = 'common';

    $locale = $this->_getLocale();

    $content_var = $code->getTempVarRef();

    $code->writePhp($content_var . ' = ob_get_contents();');
    $code->writePhp('ob_end_clean();');

    $code->writePhp("echo strings :: get($content_var, '{$file}', $locale);");
  }

  function _getLocale()
  {
    if($locale = $this->getAttribute('locale'))
      return "'$locale'";

    if($locale_type = $this->getAttribute('locale_type'))
    {
      if(strtolower($locale_type) == 'management')
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
      else
        $locale_constant = 'CONTENT_LOCALE_ID';
    }
    else
      $locale_constant = 'CONTENT_LOCALE_ID';

    return "constant('$locale_constant')";
  }
}

?>