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
class CoreRequestTransferTagInfo
{
  var $tag = 'core:REQUEST_TRANSFER';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'core_request_transfer_tag';
}

registerTag(new CoreRequestTransferTagInfo());

class CoreRequestTransferTag extends ServerTagComponentTag
{
  function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/request_transfer_component';
  }

  function preParse()
  {
    if (! array_key_exists('attributes', $this->attributes) ||  empty($this->attributes['attributes']))
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'attributes',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));

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

  function generateContents($code)
  {
    $content = '$' . $code->getTempVariable();

    $code->writePhp('ob_start();');

    parent :: generateContents($code);

    $code->writePhp("{$content} = ob_get_contents();ob_end_clean();");

    $code->writePhp($this->getComponentRefCode() . "->appendRequestAttributes({$content});");

    $code->writePhp("echo {$content};");
  }
}

?>