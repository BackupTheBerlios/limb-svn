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

$taginfo =& new TagInfo('limb:PARAMETER', 'LimbParameterTag');
$taginfo->setEndTag(ENDTAG_FORBIDDEN);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbParameterTag extends SilentCompilerDirectiveTag
{
  function prepare()
  {
    foreach(array_keys($this->attributeNodes) as $key)
    {
      $name = $this->attributeNodes[$key]->name;

      if($this->parent->hasAttribute($name))
        $this->parent->removeAttribute($name);

      $this->parent->setAttribute($name, $this->attributeNodes[$key]->getValue());
    }
  }
}

?>