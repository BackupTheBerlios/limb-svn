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
require_once(LIMB_DIR . '/core/db/IteratorDbDecorator.class.php');

// TODO: Think how to paginate this SimpleACLAccessRecordSet correctly

class SimpleACLAccessRecordSet extends IteratorDbDecorator
{
  var $action = 'display';
  var $authorizer;

  var $array_dataset;

  function valid()
  {
    return $this->array_dataset->valid();
  }

  function next()
  {
    $this->array_dataset->next();
  }

  function & current()
  {
    return $this->array_dataset->current();
  }

  function rewind()
  {
    parent :: rewind();

    $this->_applyAccessPolicy();

    $this->array_dataset->rewind();
  }

  function _applyAccessPolicy()
  {
    $this->array_dataset = new ArrayDataset(array());

    $authorizer =& $this->getAuthorizer();
    $records = array();

    for($this->iterator->rewind(); $this->iterator->valid(); $this->iterator->next())
    {
      $record =& $this->iterator->current();
      if($authorizer->canDo($this->action, $record))
        $record->set('is_accessible', true);
      else
        $record->set('is_accessible', false);

      $records[] =& $record->export();
    }

    $this->array_dataset->importDataSetAsArray($records);
  }

  function setAction($action)
  {
    $this->action = $action;
  }

  function setAuthorizer(&$authorizer)
  {
    $this->authorizer =& $authorizer;
  }

  function & getAuthorizer()
  {
    if (is_object($this->authorizer))
      return $this->authorizer;

    $toolkit =& Limb :: toolkit('SimpleACL');
    $this->authorizer =& $toolkit->getAuthorizer();

    return $this->authorizer;
  }
}

?>
