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
class MetadataCharsetTagInfo
{
  public $tag = 'metadata:CHARSET';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'metadata_charset_tag';
}

registerTag(new MetadataCharsetTagInfo());

class MetadataCharsetTag extends CompilerDirectiveTag
{
  public function generateContents($code)
  {
    $locale = '$' . $code->getTempVariable();

    $code->writePhp($locale . ' = Limb :: toolkit()->getLocale(CONTENT_LOCALE_ID);');
    $code->writePhp("echo '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=' . {$locale}->get_charset() . '\">';");
  }
}

?>