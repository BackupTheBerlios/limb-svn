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

define('ENDTAG_REQUIRED', 1);
define('ENDTAG_OPTIONAL', 2);
define('ENDTAG_FORBIDDEN', 3);

/**
* Registers information about a compile time tag in the global tag dictionary.
* This function is called from the respective compile time component class
* file.
*/
function registerTag($taginfo)
{
  $GLOBALS['tag_dictionary']->registerTag($taginfo);
}

/**
* The tag_dictionary, which exists as a global variable, acting as a registry
* of compile time components.
*/
class TagDictionary
{
  /**
  * Associative array of tag_info objects
  */
  var $tag_information = array();
  /**
  * Indexed array containing registered tag names
  */
  var $tag_list = array();

  /**
  * Registers a tag in the dictionary, called from the global register_tag()
  * function.
  */
  function registerTag($taginfo)
  {
    $tag = strtolower($taginfo->tag);
    $this->tag_list[] = $tag;
    $this->tag_information[$tag] = $taginfo;
  }

  /**
  * Gets the tag information about a given tag.
  * Called from the source_file_parser
  */
  function getTagInfo($tag)
  {
    if(isset($this->tag_information[strtolower($tag)]))
      return $this->tag_information[strtolower($tag)];
  }

  /**
  * Gets the list of a registered tags.
  * Called from the source_file_parser
  */
  function getTagList()
  {
    return $this->tag_list;
  }
}

?>