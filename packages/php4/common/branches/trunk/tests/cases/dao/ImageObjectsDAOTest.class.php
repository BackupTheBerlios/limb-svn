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
require_once(dirname(__FILE__) . '/../../../dao/ImageObjectsDAO.class.php');
require_once(LIMB_DIR . '/core/db/SimpleSelectSQL.class.php');

class ImageObjectsDAOTest extends LimbTestCase
{
  var $dao;

  function ImageObjectsDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->dao = new ImageObjectsDAO();
  }

  function testFetch()
  {
    $sql = new SimpleSelectSQL('whatever');
    $this->dao->setSQL($sql);
    $rs = $this->dao->fetch();
    $this->assertIsA($rs, 'ImageObjectsRecordSet');
  }
}

?>