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
class CoreIncludeTagInfo
{
  public $tag = 'core:INCLUDE';
  public $end_tag = ENDTAG_FORBIDDEN;
  public $tag_class = 'core_include_tag';
}

registerTag(new CoreIncludeTagInfo());

/**
* Include another template into the current template
*/
class CoreIncludeTag extends CompilerDirectiveTag
{
  protected $resolved_source_file;

  public function preParse()
  {
    global $tag_dictionary;
    if (! array_key_exists('file', $this->attributes) || 
        empty($this->attributes['file']))
    {
      throw new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'file',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
    $file = $this->attributes['file'];

    if (!$this->resolved_source_file = resolveTemplateSourceFileName($file))
    {
      throw new WactException('missing file',
          array('tag' => $this->tag,
          'srcfile' => $file,
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }

    if (array_key_exists('literal', $this->attributes))
    {
      $literal_component = new TextNode(readTemplateFile($this->resolved_source_file));
      $this->addChild($literal_component);
    }
    else
    {
      $sfp = new SourceFileParser($this->resolved_source_file, $tag_dictionary);
      $sfp->parse($this);
    }
    return PARSER_FORBID_PARSING;
  }

  public function generateContents($code)
  {
    if($this->isDebugEnabled())
    {
      $code->writeHtml("<div class='debug-tmpl-include'>");

      $this->_generateDebugEditorLinkHtml($code, $this->resolved_source_file);
    }

    parent :: generateContents($code);

    if($this->isDebugEnabled())
      $code->writeHtml('</div>');
  }
}

?>
