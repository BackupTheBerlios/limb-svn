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
require_once(WACT_ROOT . '/template/tags/form/form.tag.php');

$taginfo =& new TagInfo('limb:FORM', 'LimbFormTag');
$taginfo->setCompilerAttributes(array('useknown'));//???
$taginfo->setDefaultLocation(LOCATION_SERVER);
TagDictionary::registerTag($taginfo, __FILE__);

class LimbFormTag extends FormTag
{
  function prepare()
  {
    parent :: prepare();

    if(!$form_name = $this->getAttribute('name'))//should we leave it like that?
      return;

    $this->_renameChildren($form_name, $this->children);
  }

  function _renameChildren($form_name, &$children)
  {
    foreach($children as $child_id => $child)
    {
      if(is_a($child, 'ControlTag') && ($name = $child->getAttribute('name')))
      {
        $children[$child_id]->removeAttribute('name');
        $children[$child_id]->setAttribute('name', $form_name . $this->_makeWrappedName($name));
      }

      if(sizeof($children[$child_id]->children) > 0)
        $this->_renameChildren($form_name, $children[$child_id]->children);
    }
  }

  function _makeWrappedName($name)
  {
    return preg_replace('/^([^\[\]]+)(\[.*\])*$/', "[\\1]\\2", $name);
  }

  function preGenerate(&$code)
  {
    $this->tag = 'form';

    parent :: preGenerate($code);
  }
}
?>
