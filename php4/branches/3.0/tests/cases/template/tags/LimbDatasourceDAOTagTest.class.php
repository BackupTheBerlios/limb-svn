<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbDAOTagTest.class.php 1140 2005-03-05 10:04:56Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/DAO/DAO.class.php');

Mock :: generate('LimbBaseToolkit');
Mock :: generate('SQLBasedDAO');

class LimbDatasourceDAOTagTestCase extends LimbTestCase
{
  var $ds;
  var $toolkit;

  function LimbDatasourceDAOTagTestCase()
  {
    parent :: LimbTestCase('limb datasource dao tag case');
  }

  function setUp()
  {
    $this->ds =& new MockSQLBasedDAO($this);
    $this->toolkit =& new MockLimbBaseToolkit($this);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->ds->tally();
    $this->toolkit->tally();

    Limb :: restoreToolkit();

    ClearTestingTemplates();
  }

  function testSingleTarget()
  {
    $template = '<limb:DATASOURCE_DAO target="testTarget" class="TestDAO" />' .
                '<core:DATASOURCE id="testTarget">{$username}</core:DATASOURCE>';

    RegisterTestingTemplate('/limb/datasource_dao.html', $template);

    $page =& new Template('/limb/datasource_dao.html');

    $this->toolkit->setReturnReference('createDAO', $this->ds, array('TestDAO'));

    $data = array ('username'=>'joe');
    $datasource =& new Dataspace();
    $datasource->import($data);

    $this->ds->expectOnce('fetch');
    $this->ds->setReturnReference('fetch', $datasource);

    $this->assertEqual($page->capture(), 'joe');
  }

  function testMultipleTargets()
  {
    $template = '<limb:DATASOURCE_DAO target="testTarget1,testTarget2" class="TestDAO" />' .
                '<core:DATASOURCE id="testTarget1">{$username}</core:DATASOURCE>' .
                '<core:DATASOURCE id="testTarget2">{$secondname}</core:DATASOURCE>';

    RegisterTestingTemplate('/limb/datasource_dao_multiple_targets.html', $template);

    $page =& new Template('/limb/datasource_dao_multiple_targets.html');

    $this->toolkit->setReturnReference('createDAO', $this->ds, array('TestDAO'));

    $data = array('username'=>'joe', 'secondname' => 'fisher');
    $datasource =& new Dataspace();
    $datasource->import($data);

    $this->ds->expectOnce('fetch');
    $this->ds->setReturnReference('fetch', $datasource);

    $this->assertEqual($page->capture(), 'joefisher');
  }

}
?>
