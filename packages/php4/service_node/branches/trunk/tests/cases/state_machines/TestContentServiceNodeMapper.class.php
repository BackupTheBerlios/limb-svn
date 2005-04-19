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
require_once(LIMB_SERVICE_NODE_DIR . '/data_mappers/ContentServiceNodeMapper.class.php');

class TestContentServiceNodeMapper extends ContentServiceNodeMapper
{
  function TestContentServiceNodeMapper()
  {
    parent :: ContentServiceNodeMapper('OneTableObjectMapperTest');
  }
}

?>