<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbForm.tag.php 1013 2005-01-12 12:13:22Z pachanga $
*
***********************************************************************************/
$taginfo =& new TagInfo('limb:select_options_source', 'LimbSelectOptionsSource');
$taginfo->setDefaultLocation(LOCATION_SERVER);
$taginfo->setEndTag(ENDTAG_FORBIDDEN);
$taginfo->setKnownParent('FormTag');

TagDictionary::registerTag($taginfo, __FILE__);

class LimbSelectOptionsSource extends ServerDataComponentTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%core/template/components/form/LimbSelectOptionsSourceComponent.class.php';
  var $runtimeComponentName = 'LimbSelectOptionsSourceComponent';

  function preParse()
  {
    $target = $this->getAttribute('target');
    if (empty($target))
    {
      $this->raiseCompilerError('MISSINGREQUIREATTRIBUTE',
                                array('attribute' => 'target'));
    }

    return PARSER_FORBID_PARSING;
  }

  function generateContents(&$code)
  {
    if(!$target =& $this->parent->findChild($this->getAttribute('target')))
    {
      $this->raiseCompilerError('COMPONENTNOTFOUND',
                                array('ServerId' => 'target'));
      return;
    }

    if(!is_a($target, 'SelectTag'))
    {
      $this->raiseCompilerError('MISSINGCHILDTAG',
                                array('childtag' => 'SelectTag'));
      return;
    }

    $code->writePHP($target->getComponentRefCode() . '->setChoices(' . $this->getComponentRefCode(). '->getChoices());');
  }
}
?>
