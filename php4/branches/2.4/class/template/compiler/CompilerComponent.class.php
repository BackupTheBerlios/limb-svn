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
* Base class for compile time components. Compile time component methods are
* called by the template parser source_file_parser.<br />
* Note this in the comments for this class, parent and child refer to the XML
* heirarchy in the template, as opposed to the PHP class tree.
*/
class CompilerComponent
{
  /**
  * XML attributes of the tag
  */
  var $attributes = array();
  /**
  * child compile-time components
  */
  var $children = array();
  var $vars = array();
  /**
  * Parent compile-time component
  */
  var $parent = null;
  /**
  * Stores the identifying component ID
  */
  var $server_id;
  /**
  * Name of the XML tag as it appears in the template. This would include
  * the namespace prefix, if applicable.
  */
  var $tag;
  /**
  * Used to identify the source template file, when generating compile time
  * error messages.
  */
  var $source_file;
  /**
  * Used to indentify the line number where a compile time error occurred.
  */
  var $starting_line_no;
  /**
  * Instance of a CoreWraptag
  */
  var $wrapping_component;
  /**
  * Defines whether the tag is allowed to have a closing tag
  */
  var $has_closing_tag;

  /**
  * Sets the XML attributes for this component (as extracted from the
  * template)
  */
  function setAttributes($attrib)
  {
    $this->attributes = $attrib;
  }

  function setSourceFile($source_file)
  {
    $this->source_file = $source_file;
  }
  /**
  * Remove an attribute from the list
  * @param string name of attribute
  */
  function removeAttribute($attrib)
  {
    unset($this->attributes[strtolower($attrib)]);
  }

  function hasAttribute($attrib)
  {
    return isset($this->attributes[strtolower($attrib)]);
  }

  /**
  * Get the value of the XML id attribute
  */
  function getClientId()
  {
    if (isset($this->attributes['id']))
    {
      return $this->attributes['id'];
    }
  }

  /**
  * Returns the identifying server ID. It's value it determined in the
  * following order;
  * <ol>
  * <li>The XML id attribute in the template if it exists</li>
  * <li>The value of $this->server_id</li>
  * <li>An ID generated by the get_new_server_id() function</li>
  * </ol>
  */
  function getServerId()
  {
    if (!empty($this->attributes['id']))
    {
      return $this->attributes['id'];
    }
    else if (!empty($this->server_id))
    {
      return $this->server_id;
    }
    else
    {
      $this->server_id = getNewServerId();
      return $this->server_id;
    }
  }

  /**
  * Adds a child component, by reference, to the array of children
  */
  function addChild($child)
  {
    $child->parent = $this;
    $this->children[] = $child;
  }

  /**
  * Removes a child component, given it's ServerID
  */
  function removeChild($server_id)
  {
    foreach(array_keys($this->children) as $key)
    {
      $child = $this->children[$key];
      if ($child->getServerId() == $server_id)
      {
      unset($this->children[$key]);
        return $child;
      }
    }
  }

  /**
  * Returns a child component, given it's ServerID
  */
  function findChild($server_id)
  {
    foreach(array_keys($this->children) as $key)
    {
      if ($this->children[$key]->getServerId() == $server_id)
        return $this->children[$key];
      else
      {
        if($result = $this->children[$key]->findChild($server_id))
          return $result;
      }
    }
    return false;
  }

  /**
  * Returns a child component, given it's compile time component class
  */
  function findChildByClass($class)
  {
    foreach(array_keys($this->children) as $key)
    {
      if(is_a($this->children[$key], $class))
      {
        return $this->children[$key];
      }
      else
      {
        $result = $this->children[$key]->findChildByClass($class);
        if ($result)
        {
          return $result;
        }
      }
    }
    return false;
  }

  /**
  * Returns a child component, given it's compile time component class
  */
  function findImmediateChildByClass($class)
  {
    foreach(array_keys($this->children) as $key)
    {
      if (is_a($this->children[$key], $class))
      {
        return $this->children[$key];
      }
    }
    return false;
  }

  /**
  * Returns a parent component, recursively searching parents by their
  * compile time component class name
  */
  function findParentByClass($class)
  {
    $parent = $this->parent;
    while ($parent &&  !(is_a($parent, $class)))
    {
      $parent = $parent->parent;
    }
    return $parent;
  }

  /**
  * Calls the prepare method for each child component, which will override
  * this method it it's concrete implementation. In the subclasses, prepare
  * will set up compile time variables. For example the CoreWraptag uses
  * the prepare method to assign itself as the wrapping component.
  */
  function prepare()
  {
    foreach($this->children as $key => $child)
    {
      $this->children[$key]->prepare();
    }
  }

  /**
  * Used to perform some error checking on the source template, such as
  * examining the tag hierarchy and triggering an error if a tag is
  * incorrectly nested. Concrete implementation is in subclasses
  */
  function checkNestingLevel()
  {
  }

  /**
  * Provides instruction to the template parser, while parsing is in
  * progress, telling it how it should handle the tag. Subclasses of
  * compiler_component will return different instructions.<br />
  * Available instructions are;
  * <ul>
  * <li>PARSER_REQUIRE_PARSING - default in this class. tag must be parsed</li>
  * <li>PARSER_FORBID_PARSING - tag may not be parsed</li>
  * <li>PARSER_ALLOW_PARSING - tag may can be parsed</li>
  * </ul>
  * In practice, the parser currently only pays attention to the
  * PARSER_FORBID_PARSING instruction.<br />
  * Also used to perform error checking on template related to the syntax of
  * the concrete tag implementing this method.
  */
  function preParse()
  {
    return PARSER_REQUIRE_PARSING;
  }

  /**
  * If a parent compile time component exists, returns the value of the
  * parent's get_dataspace() method, which will be a concrete implementation
  */
  function getDataspace()
  {
    if (isset($this->parent))
    {
      return $this->parent->getDataspace();
    }
  }

  /**
  * Gets the parent in the dataspace, if one exists
  */
  function getParentDataspace()
  {
    $dataspace = $this->getDataspace();
    if (isset($dataspace->parent))
    {
      return $dataspace->parent->getDataspace();
    }
  }

  /**
  * Gets a root dataspace
  */
  function getRootDataspace()
  {
    $root = $this;
    while ($root->parent != null)
    {
      $root = $root->parent;
    }
    return $root;
  }

  /**
  * Gets the dataspace reference code of the parent
  */
  function getDataspaceRefCode()
  {
    return $this->parent->getDataspaceRefCode();
  }

  /**
  * Gets the component reference code of the parent. This is a PHP string
  * which is used in the compiled template to reference the component in
  * the hierarchy at runtime
  */
  function getComponentRefCode()
  {
    return $this->parent->getComponentRefCode();
  }

  /**
  * Calls the generate_constructor() method of each child component
  */
  function generateConstructor($code)
  {
    foreach(array_keys($this->children) as $key)
    {
      $this->children[$key]->generateConstructor($code);
    }
  }


  /**
  * Calls the generate() method of each child component
  */
  function generateContents($code)
  {
    foreach(array_keys($this->children) as $key)
    {
      $this->children[$key]->generate($code);
    }
  }

  /**
  * Pre generation method, calls the wrapping_component
  * generate_wrapper_prefix() method if the component exists
  */
  function preGenerate($code)
  {
    if (isset($this->wrapping_component))
    {
      if($this->isDebugEnabled())
      {
        $code->writeHtml("<div class='debug-tmpl-container'>");

        $this->_generateDebugEditorLinkHtml($code, $this->wrapping_component->resolved_source_file);
      }

      $this->wrapping_component->generateWrapperPrefix($code);
    }
  }

  function _generateDebugEditorLinkHtml($code, $file_path)
  {
    if(!defined('WS_SCRIPT_WRITTEN'))
    {

      $code->writeHtml('	<SCRIPT LANGUAGE="JScript">
                          function run_template_editor(path)
                          {
                            WS = new ActiveXObject("WScript.shell");
                            WS.exec("uedit32.exe " + path);
                          }
                          </SCRIPT>');

      define('WS_SCRIPT_WRITTEN', true);
    }

    if(Fs :: isPathRelative($file_path))
    {
      $items = Fs :: explodePath($_SERVER['PATH_TRANSLATED']);
      array_pop($items);

      $file_path = Fs :: path($items) . Fs :: separator() . $file_path;
    }

    $file_path = addslashes(Fs :: cleanPath($file_path));
    $code->writeHtml("<a href='#'><img onclick='runTemplateEditor(\"{$file_path}\");' src='/shared/images/i.gif' alt='{$file_path}' title='{$file_path}' border='0'></a>");
  }

  /**
  * Post generation method, calls the wrapping_component
  * generate_wrapper_postfix() method if the component exists
  */
  function postGenerate($code)
  {
    if (isset($this->wrapping_component))
    {
      $this->wrapping_component->generateWrapperPostfix($code);

      if($this->isDebugEnabled())
        $code->writeHtml('</div>');
    }
  }

  /**
  * Calls the local pre_generate(), generate_contents() and post_generate()
  * methods.
  */
  function generate($code)
  {
    $this->preGenerate($code);
    $this->generateContents($code);
    $this->postGenerate($code);
  }

  function isDebugEnabled()
  {
    return (defined('DEBUG_TEMPLATE_ENABLED') &&  constant('DEBUG_TEMPLATE_ENABLED'));
  }
}

?>
