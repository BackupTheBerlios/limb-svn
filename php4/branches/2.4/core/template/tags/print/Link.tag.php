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
  var $tag = 'print:LINK';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'print_link_tag';
}

registerTag(new PrintLinkTagInfo());

class PrintLinkTag extends CompilerDirectiveTag
{
  function generateContents($code)
  {
    $mapped = '$' . $code->getTempVariable();

    $code->writePhp("\$toolkit =& Limb :: toolkit();
                    \$fetcher =& \$toolkit->getFetcher();
                    {$mapped} = \$fetcher->fetchRequestedObject(\$toolkit->getRequest());");

    $code->writePhp("if(isset({$mapped}['actions']) && array_key_exists('print_version', {$mapped}['actions'])){");

    $code->writePhp($this->getDataspaceRefCode() . "->set('link', {$mapped}['path'] . '?action=print_version');");

    parent :: generateContents($code);

    $code->writePhp('}');
  }
}

?>