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
class PrintLinkTagInfo
{
  public $tag = 'print:LINK';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'print_link_tag';
}

registerTag(new PrintLinkTagInfo());

class PrintLinkTag extends CompilerDirectiveTag
{
  public function generateContents($code)
  {
    $mapped = '$' . $code->getTempVariable();

    $code->writePhp("{$mapped} = Limb :: toolkit()->getFetcher()->fetchRequestedObject(Limb :: toolkit()->getRequest());");

    $code->writePhp("if(isset({$mapped}['actions']) && array_key_exists('print_version', {$mapped}['actions'])){");

    $code->writePhp($this->getDataspaceRefCode() . "->set('link', {$mapped}['path'] . '?action=print_version');");

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>