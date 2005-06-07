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
require_once(LIMB_DIR . '/core/dao/DAO.class.php');
require_once(LIMB_DIR . '/core/data_mappers/AbstractDataMapper.class.php');
require_once(LIMB_DIR . '/core/orm/ProxyDomainObjectCollection.class.php');
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');

Mock :: generate('DAO');
Mock :: generate('AbstractDataMapper');

class ProxyTestMapper extends MockAbstractDataMapper
{
  function load(&$ds, &$object)
  {
    parent :: load($ds, $object);
    $object->set('id', $ds->get('id'));
  }
}

class ProxyTestDataPager
{
  function setPagedDataSet(&$dataset) {}
  function getStartingItem() {}
  function getItemsPerPage() {}
}

Mock::generate('ProxyTestDataPager', 'MockPager');

class ProxyDomainObjectCollectionTest extends LimbTestCase
{
  function ProxyDomainObjectCollectionTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function testRewind()
  {
    $dao = new MockDAO($this);
    $mapper = new ProxyTestMapper($this);

    $proxy = new ProxyDomainObjectCollection($dao, $mapper, new LimbHandle('Object'));

    $raw_array = array(array('id' => 1), array('id' => 2));

    $dao->expectOnce('fetch');
    $dao->setReturnValue('fetch', $dataset = new ArrayDataSet($raw_array));

    $ds1 = new DataSpace();
    $ds1->import($raw_array[0]);

    $ds2 = new DataSpace();
    $ds2->import($raw_array[1]);

    $mapper->expectArgumentsAt(0, 'load', array($ds1, new Object()));
    $mapper->expectArgumentsAt(1, 'load', array($ds2, new Object()));

    $proxy->rewind();

    $expected1 = new Object();
    $expected1->set('id', 1);

    $this->assertEqual($proxy->current(), $expected1);

    $proxy->next();

    $expected2 = new Object();
    $expected2->set('id', 2);

    $this->assertEqual($proxy->current(), $expected2);

    $proxy->next();

    $this->assertFalse($proxy->valid());

    $dao->tally();
    $mapper->tally();
  }

  function testAdd()
  {
    $dao = new MockDAO($this);
    $mapper = new ProxyTestMapper($this);

    $proxy = new ProxyDomainObjectCollection($dao, $mapper, new LimbHandle('Object'));

    $raw_array = array(array('id' => 1));

    $dao->expectOnce('fetch');
    $dao->setReturnValue('fetch', $dataset = new ArrayDataSet($raw_array));

    $ds1 = new DataSpace();
    $ds1->import($raw_array[0]);

    $mapper->expectOnce('load', array($ds1, new Object()));

    $added_object = new Object();
    $added_object->set('id', 2);

    $proxy->add($added_object);

    $proxy->rewind();

    $fetched_object = new Object();
    $fetched_object->set('id', 1);

    $this->assertEqual($proxy->current(), $fetched_object);

    $proxy->next();

    $this->assertEqual($proxy->current(), $added_object);

    $proxy->next();

    $this->assertFalse($proxy->valid());

    $dao->tally();
    $mapper->tally();
  }
}

?>
