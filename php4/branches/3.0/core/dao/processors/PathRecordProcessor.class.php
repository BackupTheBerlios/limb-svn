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
class PathRecordProcessor
{
  var $translator;

  function PathRecordProcessor()
  {
    $toolkit =& Limb :: toolkit();
    $this->translator =& $toolkit->getPath2Idtranslator();
  }

  function process(&$record)
  {
    if($path = $record->get('path'))
      return $path;

    if(($identifier = $record->get('_node_identifier')) &&
       ($parent_node_id = $record->get('_node_parent_id')))
    {
      $path = $this->_getPathUsingParentNodeId($identifier, $parent_node_id);
    }
    elseif($node_id = $record->get('_node_id'))
    {
      $path = $this->_getPathUsingNodeId($node_id);
    }

   if($path)
    $record->set('_node_path', $path);
  }

  function _getPathUsingParentNodeId($identifier, $parent_node_id)
  {
    if(!($parent_path = $this->translator->getPathToNode($parent_node_id)))
      return;

    return rtrim($parent_path, '/') . '/' . $identifier;
  }

  function _getPathUsingNodeId($node_id)
  {
    return $this->translator->getPathToNode($node_id);
  }
}

?>
