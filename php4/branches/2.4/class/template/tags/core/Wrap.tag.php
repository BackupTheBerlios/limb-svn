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
class CoreWrapTagInfo
{
  var $tag = 'core:WRAP';
  var $end_tag = ENDTAG_FORBIDDEN;
  var $tag_class = 'core_wrap_tag';
}

registerTag(new CoreWrapTagInfo());

/**
* Merges the current template with a wrapper template, the current
* template being inserted into the wrapper at the point where the
* wrap tag exists.
*/
class CoreWrapTag extends CompilerDirectiveTag
{
  var $resolved_source_file;

  var $keylist;

  function checkNestingLevel()
  {
    if ($this->findParentByClass('core_wrap_tag'))
    {
      return throw(new WactException('bad self nesting',
          array('tag' => $this->tag,
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }
  }

  function preParse()
  {
    global $tag_dictionary;
    $file = $this->attributes['file'];
    if (!isset($this->attributes['file']) ||  !$this->attributes['file'])
    {
      return throw(new WactException('missing required attribute',
          array('tag' => $this->tag,
          'attribute' => 'file',
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }

    if (!$this->resolved_source_file = resolveTemplateSourceFileName($file))
    {
      return throw(new WactException('missing file',
          array('tag' => $this->tag,
          'srcfile' => $file,
          'file' => $this->source_file,
          'line' => $this->starting_line_no)));
    }

    $sfp = new SourceFileParser($this->resolved_source_file, $tag_dictionary);
    $sfp->parse($this);
    return PARSER_FORBID_PARSING;
  }

  function prepare()
  {
    $this->parent->wrapping_component = $this;

    parent :: prepare();
  }

  function generateWrapperPrefix($code)
  {
    $this->keylist = array_keys($this->children);
    $name = $this->attributes['placeholder'];
    reset($this->keylist);
    while (list(, $key) = each($this->keylist))
    {
      $child = $this->children[$key];
      if ($child->getServerId() == $name)
      {
        break;
      }
      $child->generate($code);
    }
  }

  function generateWrapperPostfix($code)
  {
    while (list(, $key) = each($this->keylist))
    {
      $this->children[$key]->generate($code);
    }
  }

  /**
  * By the time this is called we have already called generate
  * on all of our children, so does nothing
  */
  function generate($code)
  {
    // By the time this is called we have already called generate
    // on all of our children.
  }
}

?>