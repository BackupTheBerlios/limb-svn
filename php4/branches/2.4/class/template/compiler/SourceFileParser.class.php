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
/**
* Define compile component states which determine parse behaviour
*/
define('PARSER_REQUIRE_PARSING', true);
define('PARSER_FORBID_PARSING', false);
define('PARSER_ALLOW_PARSING', null);

/**
* The source template parser which uses a regular expression engine
*/
class SourceFileParser
{
  /**
  * The contents of the source template as a string
  */
  var $rawtext;
  /**
  * path and filename of source template
  */
  var $source_file;
  /**
  * Reference to the global instance of the tag_dictionary
  */
  var $tag_dictionary;
  /**
  * Current line number of parser cursor within the raw text
  */
  var $cur_line_no;
  /**
  * Regex pattern to match an opening tags which are components,
  * based on the contents of the tag dictionary.
  */
  var $tag_starting_pattern;
  /**
  * Regex pattern to match opening tag attributes
  */
  var $attribute_pattern;
  /**
  * Regex pattern to match the contents of a tag.
  */
  var $variable_reference_pattern;

  function SourceFileParser($sourcefile, $tag_dictionary)
  {
    $this->source_file = $sourcefile;
    $this->tag_dictionary = $tag_dictionary;
    $this->rawtext = $this->readTemplateFile($sourcefile);
    $this->cur_line_no = 1;
    $this->text = '';

    $this->initializeTagStartingPattern();
    $this->initializeAttributePattern();
    $this->initializeVariableReferencePattern();
  }
  /**
  * Builds the tag starting regex pattern, which "spots" all tags registered
  * in the  $tag_dictionary
  */
  function initializeTagStartingPattern()
  {
    $tag_list = $this->tag_dictionary->getTagList();

    $tag_starting_pattern = '/';
    $tag_starting_pattern .= '^(.*)';
    $tag_starting_pattern .= preg_quote('<', '/');
    $tag_starting_pattern .= '(' . preg_quote('/', '/') . ')?';
    $tag_starting_pattern .= '(';
    $sep = '';

    foreach ($tag_list as $tag)
    {
      $tag_starting_pattern .= $sep;
      $tag_starting_pattern .= preg_quote($tag, '/');
      $sep = '|';
    }
    $tag_starting_pattern .= ')';
    $tag_starting_pattern .= '(\s+|\/?' . preg_quote('>', '/') . ')';

    $tag_starting_pattern .= '/Usi';

    $this->tag_starting_pattern = $tag_starting_pattern;
  }
  /**
  * Builds the regex for fetching contents of tags
  */
  function initializeVariableReferencePattern()
  {
    $this->variable_reference_pattern = '/^(.*){(\$|\#|\^)([\w\[\]\'\"]+)}(.*)$/Usi';
  }
  /**
  * Builds the attribute spotting regular expression
  */
  function initializeAttributePattern()
  {
    $this->attribute_pattern = "/^(\\w+)\\s*(=\\s*(\"|')?((?(3)[^\\3]*?|[^\\s]*))(?(3)\\3))?\\s*/";
  }
  /**
  * Used to find tag components in the template
  */
  function matchText($pattern, &$match)
  {
    if (preg_match($pattern, $this->rawtext, $match))
    {
      $this->rawtext = substr($this->rawtext, strlen($match[0]));
      $this->cur_line_no += preg_match_all("/\r\n|\n|\r/", $match[0], $discarded);
      return true;
    }
    else
    {
      return false;
    }
  }
  /**
  * Used to parse the attributes of a component tag
  */
  function parseAttributes($component)
  {
    $attributes = array();

    while ($this->matchText($this->attribute_pattern, $attribute_match))
    {
      $attrib_name = strtolower($attribute_match[1]);
      if (!empty($attribute_match[2]))
      {
        $attributes[$attrib_name] = html_entity_decode($attribute_match[4]);
      }
      else
      {
        $attributes[$attrib_name] = null;
      }
    }

    $component->setAttributes($attributes);
  }

  // This does not correctly determine the line number of the variable reference.
  // The preg_match in this method should be rolled up and included in the main
  // loop of the parse() method.
  // This will require a seriously nasty regular expression.
  /**
  * Used to parse the contents of a component
  */
  function parseText($parent_component, $text)
  {
    while (preg_match($this->variable_reference_pattern, $text, $match))
    {
      if (strlen($match[1]) > 0)
      {
        $component = $this->getTextNode($match[1]);
        $parent_component->addChild($component);
      }
      $component = $this->getVariableReference();
      $component->reference = $match[3];
      $component->scope = $match[2];
      $component->source_file = $this->source_file;
      $component->starting_line_no = $this->cur_line_no;
      $parent_component->addChild($component);
      $text = $match[4];
    }
    if (strlen($text) > 0)
    {
      $component = $this->getTextNode($text);
      $parent_component->addChild($component);
    }
  }

  function checkServerId($parent_component, $component)
  {
    $tree = $parent_component;
    if (is_a($component, 'ServerTagComponentTag'))
    {
      // Move up to the root
      while (!is_null($tree->parent))
      {
        $tree = $tree->parent;
      }
    }
    elseif($tree->parent)
       $tree = $tree->parent;

    $server_id = $component->getServerId();

    if ($tree->findChild($server_id))
    {
      return throw(new WactException('dublicated component found',
          array(
            'server_id' => $server_id,
            'tag' => $component->tag,
            'file' => $component->source_file,
            'line' => $component->starting_line_no
          )));
    }
  }
  // --------------------------------------------------------------------------------
  /**
  * Used to parse (recursively) parse the source template. It is initially
  * invoked by the Compiletemplate function, the first component argument
  * being a root_compiler_component. Accesses the $tag_dictionary
  */
  function parse($parent_component)
  {
    $tag_info = null;
    $parent_component->contents = '';

    while ($this->matchText($this->tag_starting_pattern, $start_maches))
    {
      $tag = $start_maches[3];
      $this->parseText($parent_component, $start_maches[1]);
      if ($start_maches[2] == '/')
      {
        if (isset($parent_component->tag))
        {
          if ($tag != $parent_component->tag)
          {
            return throw(new WactException('unexpected close',
                array(
                  'tag' => $tag,
                  'expect_tag' => $parent_component->tag,
                  'file' => $this->source_file,
                  'line' => $this->cur_line_no
                )));
          }
          else
          {
            return;
          }
        }
        else
        {
          return throw(new WactException('unexpected close',
              array(
                'tag' => $tag,
                'file' => $this->source_file,
                'line' => $this->cur_line_no
              )));
        }
      }
      else
      {
        $tag_info = $this->tag_dictionary->getTagInfo($tag);
        $tag_class = $tag_info->tag_class;

        $component = new $tag_class();
        $component->tag = $tag;
        $component->source_file = $this->source_file;
        $component->starting_line_no = $this->cur_line_no;

        if ($start_maches[4] != '>')
        {
          $this->parseAttributes($component);

          if (!$this->matchText('/^\/?>/', $start_maches))
          {
            return throw(new WactException('expecting >',
                array(
                  'tag' => $component->tag,
                  'file' => $this->source_file,
                  'line' => $this->cur_line_no
                )));
          }
        }

        $this->checkServerId($parent_component, $component);
        $parent_component->addChild($component);
        $component->checkNestingLevel();

        $parsing_policy = $component->preParse();
        if ($tag_info->end_tag == ENDTAG_REQUIRED)
        {
          if ($parsing_policy == PARSER_FORBID_PARSING)
          {
            if ($this->matchText('/^(.*)' . preg_quote('</' . $component->tag . '>', '/') . '/Usi', $literal_match))
            {
              $literal_component = $this->getTextNode($literal_match[1]);
              $component->addChild($literal_component);
            }
            else
            {
              return throw(new WactException('missing close tag',
                  array(
                    'tag' => $component->tag,
                    'file' => $this->source_file,
                    'line' => $this->cur_line_no
                  )));
            }
          }
          else
          {
            $this->parse($component);
          }
          $component->has_closing_tag = true;
        }
        else
        {
          $component->has_closing_tag = false;
        }
      }
    }

    if (isset($parent_component->tag))
    {
      $parenttag_info = $this->tag_dictionary->getTagInfo($parent_component->tag);
      if ($parenttag_info->end_tag != ENDTAG_REQUIRED)
      {
        $this->parseText($parent_component, $this->rawtext);
      }
      else
      {
        return throw(new WactException('missing close tag',
            array(
              'tag' => $parent_component->tag,
              'file' => $this->source_file,
              'line' => $this->cur_line_no
            )));
      }
    }
    else
    {
      $this->parseText($parent_component, $this->rawtext);
    }
  }
  /**
  * Provide local method of same name to help with Unit testing
  */
  function readTemplateFile($sourcefile)
  {
    return readTemplateFile($sourcefile);
  }
  /**
  * Returns an instance of text_node
  */
  function getTextNode($text)
  {
    return new TextNode($text);
  }
  /*
  * Returns an instance of variable_reference
  */
  function getVariableReference()
  {
    return new VariableReference();
  }
}

?>