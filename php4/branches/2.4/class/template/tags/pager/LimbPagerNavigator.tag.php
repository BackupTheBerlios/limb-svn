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
$taginfo =& new TagInfo('limb:pager:NAVIGATOR', 'LimbPagerNavigatorTag');
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbPagerNavigatorTag extends ServerComponentTag
{
  var $runtimeIncludeFile = '%LIMB_DIR%/class/template/components/LimbPagerComponent.class.php';
  var $runtimeComponentName = 'LimbPagerComponent';

  var $mirror;

  function preGenerate(&$code)
  {
    parent::preGenerate($code);

    $code->writePhp($this->getComponentRefCode() . '->prepare();');
  }

  function generateConstructor(&$code)
  {
    parent :: generateConstructor($code);

    $items = $this->getAttribute('items');
    if (!empty($items))
      $code->writePhp($this->getComponentRefCode() . '->setItemsPerPage(' . $items . ');');

    $pages_per_section = $this->getAttribute('pages_per_section');
    if (!empty($pages_per_section))
      $code->writePhp($this->getComponentRefCode() . '->setPagesPerSection(' . $pages_per_section . ');');

    $pager_prefix = $this->getAttribute('pager_prefix');
    if (!empty($pager_prefix))
      $code->writePhp($this->getComponentRefCode() . '->setPagerPrefix("' . $pager_prefix . '");');
  }

  function prepare()
  {
    parent :: prepare();

    $this->mirror = $this->getAttribute('mirror');
    if (empty($this->mirror))
      return;

    if(!$mirrored_pager = $this->parent->findChild($this->mirror))
      $this->raiseCompilerError('COMPONENTNOTFOUND',
                                array('attribute' => $this->mirror));
  }

  function getComponentRefCode()
  {
    if ($this->mirror && ($mirrored_pager =& $this->parent->findChild($this->mirror)))
    {
      return $mirrored_pager->getComponentRefCode();
    }
    else
      return parent :: getComponentRefCode();
  }
}

?>