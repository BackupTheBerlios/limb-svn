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
require_once(LIMB_DIR . '/core/data_mappers/AbstractDataMapper.class.php');
require_once(LIMB_DIR . '/core/DomainObject.class.php');
require_once(LIMB_DIR . '/core/dao/DAO.class.php');

class SimpleObjectMapper extends AbstractDataMapper{}

class SimpleObjectDAO extends DAO{}

class SimpleObject extends DomainObject
{
  var $__class_name = 'SimpleObject';

  function setTitle($title){}
  function setAnnotation($annotation){}
  function getTitle(){}
  function getAnnotation(){}
}

Mock :: generatePartial('SimpleObject', 'SpecialMockSimpleObject',
                 array('setTitle',
                       'setAnnotation',
                       'getTitle',
                       'getAnnotation'));

?>
