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
$taginfo =& new TagInfo('limb:pager:SEPARATOR', 'LimbPagerSeparatorTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbPagerSeparatorTag extends SilentCompilerDirectiveTag
{
  function checkNestingLevel()
  {
    if ($this->findParentByClass('LimbPagerSeparatorTag'))
    {
      $this->raiseCompilerError('BADSELFNESTING');
    }

    if (!$this->findParentByClass('LimbPagerListTag'))
    {
      $this->raiseCompilerError('MISSINGENCLOSURE');
    }
  }
}

?>