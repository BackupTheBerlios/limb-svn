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
$taginfo =& new TagInfo('limb:locale:Date', 'LimbLocaleDateTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

require_once(LIMB_DIR . '/core/date/Date.class.php');

class LimbLocaleDateTag extends ServerComponentTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%/core/template/components/locale/LocaleDateComponent.class.php';
  var $runtimeComponentName = 'LocaleDateComponent';

  function CheckNestingLevel()
  {
    if ($this->findParentByClass('LimbLocaleDateFormatTag'))
      $this->raiseCompilerError('BADSELFNESTING');
  }

  function preGenerate(&$code)
  {
    $code->writePhp('ob_start();');
  }

  function postGenerate(&$code)
  {
    $code->writePhp($this->getComponentRefCode() . '->prepare();');

    $locale = $this->_getLocale();
    $code->writePhp($this->getComponentRefCode() . '->setLocale("' . $locale . '");');

    if($date_locale = $this->getAttribute('date_locale'))
       $code->writePhp($this->getComponentRefCode() . '->setDateLocale("' . $date_locale . '");');
    else
       $code->writePhp($this->getComponentRefCode() . '->setDateLocale("' . $locale . '");');

    if($date_type = $this->getAttribute('date_type'))
       $code->writePhp($this->getComponentRefCode() . '->setDateType("' . $date_type . '");');

    if($format_type = $this->getAttribute('format_type'))
       $code->writePhp($this->getComponentRefCode() . '->setFormatType("' . $format_type . '");');

    if($format = $this->getAttribute('format'))
       $code->writePhp($this->getComponentRefCode() . '->setFormatString("' . $format . '");');

    $content_var = $code->getTempVarRef();

    $code->writePhp($content_var . ' = ob_get_contents();');
    $code->writePhp('ob_end_clean();');

    if($date_format = $this->getAttribute('date_format'))
      $code->writePhp($this->getComponentRefCode() . '->setDate(' . $content_var . ', "' . $date_format .'");');
    else
      $code->writePhp($this->getComponentRefCode() . '->setDate(' . $content_var . ');');

    $code->writePhp($this->getComponentRefCode() . '->format();');
  }


  function & _getLocale()
  {
    if($locale = $this->getAttribute('locale'))
      return $locale;

    if($locale_type = $this->getAttribute('locale_type'))
    {
      if(strtolower($locale_type) == 'management')
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
      else
        $locale_constant = 'CONTENT_LOCALE_ID';
    }
    else
      $locale_constant = 'CONTENT_LOCALE_ID';

    return constant($locale_constant);
  }

}

?>