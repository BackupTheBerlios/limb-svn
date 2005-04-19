<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: OneTableObjectMapper.class.php 1094 2005-02-08 13:09:14Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/data_mappers/EntityDataMapper.class.php');
require_once(LIMB_DIR . '/core/data_mappers/CompositeMapper.class.php');

// Note: abstract class!
class ContentServiceNodeMapper extends EntityDataMapper
{
  function ContentServiceNodeMapper($table_class_name)
  {
    parent :: EntityDataMapper();

    $node_mapper = new CompositeMapper();
    $node_mapper->registerMapper(new LimbHandle(LIMB_DIR . '/core/data_mappers/TreeNodeDataMapper'));
    $node_mapper->registerMapper(new LimbHandle(LIMB_DIR . '/core/data_mappers/NodeConnectionMapper'));

    $this->registerPartMapper('node', $node_mapper);
    $this->registerPartMapper('service', new LimbHandle(LIMB_DIR . '/core/data_mappers/ServiceLocationMapper'));

    $this->registerPartMapper('content', new LimbHandle(LIMB_DIR . '/core/data_mappers/OneTableObjectMapper', array($table_class_name)));
  }

  function getIdentityKeyName()
  {
    return 'oid';
  }
}

?>