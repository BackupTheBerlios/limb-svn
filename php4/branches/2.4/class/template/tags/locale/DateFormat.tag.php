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
require_once(LIMB_DIR . '/class/template/compiler/ServerComponentTag.class.php');

class LocaleDateFormatTagInfo
{
  var $tag = 'locale:DATE_FORMAT';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'locale_date_format_tag';
}

registerTag(new LocaleDateFormatTagInfo());

class LocaleDateFormatTag extends ServerComponentTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/locale_date_format_component';
  }

  function preGenerate($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->prepare();');
  }

  function generateContents($code)
  {
    if(isset($this->attributes['hash_id']))
    {

      if(isset($this->attributes['locale_type']))
      {
        $code->writePhp(
          $this->getComponentRefCode() . '->set_locale_type("' . $this->attributes['locale_type'] . '");');
      }

      if(isset($this->attributes['type']))
      {
        $code->writePhp(
          $this->getComponentRefCode() . '->set_date_type("' . $this->attributes['type'] . '");');
      }

      if(!isset($this->attributes['date_format']))
        $code->writePhp(
          $this->getComponentRefCode() . '->set_date(' . $this->getDataspaceRefCode() . '->get("' . $this->attributes['hash_id'] . '"), DATE_SHORT_FORMAT_ISO);');
      else
        $code->writePhp(
          $this->getComponentRefCode() . '->set_date(' . $this->getDataspaceRefCode() . '->get("' . $this->attributes['hash_id'] . '"), "' . $this->attributes['date_format'] . '");');

      if(isset($this->attributes['locale_format']))
      {
        $code->writePhp(
          $this->getComponentRefCode() . '->set_locale_format_type("' . $this->attributes['locale_format'] . '");');
      }
      elseif(isset($this->attributes['format']))
      {
        $code->writePhp(
          $this->getComponentRefCode() . '->set_format_string("' . $this->attributes['format'] . '");');
      }

      $code->writePhp(
        $this->getComponentRefCode() . '->format();');
    }

    parent :: generateContents($code);
  }
}

?>