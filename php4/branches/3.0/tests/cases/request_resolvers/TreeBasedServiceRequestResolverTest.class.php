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
require_once(LIMB_DIR . '/core/request_resolvers/TreeBasedServiceRequestResolver.class.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');
require_once(LIMB_DIR . '/core/services/Service.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'ToolkitTreeBasedServiceRequestResolverTestVersion',
                        array('getPath2IdTranslator'));

Mock :: generate('Path2IdTranslator');

class TreeBasedServiceRequestResolverTest extends LimbTestCase
{
  var $path2id_resolver;
  var $toolkit;
  var $db;

  function TreeBasedServiceRequestResolverTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->path2id_resolver = new MockPath2IdTranslator($this);

    $this->toolkit = new ToolkitTreeBasedServiceRequestResolverTestVersion($this);

    $conn =& $this->toolkit->getDbConnection();
    $this->db = new SimpleDb($conn);

    $this->toolkit->setReturnReference('getPath2IdTranslator', $this->path2id_resolver);

    Limb :: registerToolkit($this->toolkit);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->path2id_resolver->tally();

    Limb :: restoreToolkit();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_object');
    $this->db->delete('sys_object_to_service');
    $this->db->delete('sys_service');
  }

  function testGetServiceCantMapToId()
  {
    $resolver = new TreeBasedServiceRequestResolver();

    $request =& $this->toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = 'whatever');

    $this->path2id_resolver->expectOnce('toId', array($path));
    $this->path2id_resolver->setReturnValue('toId', null);

    $service =& $resolver->resolve($request);
    $this->assertEqual($service->getName(), '404');
  }

  function testGetServiceByOIDOk()
  {
    $resolver = new TreeBasedServiceRequestResolver();

    $request =& $this->toolkit->getRequest();
    $request->set('oid', "10c"); //checking explicit typecasting to int

    $id = 10;
    $this->path2id_resolver->expectNever('toId');

    $this->db->insert('sys_object', array('oid' => $id,
                                          'class_id' => 100));

    $this->db->insert('sys_object_to_service', array('oid' => $id,
                                         '            service_id' => $service_id = 30));

    $this->db->insert('sys_service', array('id' => $service_id,
                                           'name' => $service_name = 'TestService'));

    $service = $resolver->resolve($request);

    $this->assertEqual($service->getName(), $service_name);
  }

  function testGetServiceByPathOk()
  {
    $resolver = new TreeBasedServiceRequestResolver();

    $request =& $this->toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = 'whatever');

    $this->path2id_resolver->expectOnce('toId', array($path));
    $this->path2id_resolver->setReturnValue('toId', $id = 10);

    $this->db->insert('sys_object', array('oid' => $id,
                                          'class_id' => 100));

    $this->db->insert('sys_object_to_service', array('oid' => $id,
                                         '            service_id' => $service_id = 30));

    $this->db->insert('sys_service', array('id' => $service_id,
                                           'name' => $service_name = 'TestService'));

    $service = $resolver->resolve($request);

    $this->assertEqual($service->getName(), $service_name);
  }

  function testGetServiceNotFound()
  {
    $resolver = new TreeBasedServiceRequestResolver();

    $request =& $this->toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = 'whatever');

    $this->path2id_resolver->expectOnce('toId', array($path));
    $this->path2id_resolver->setReturnValue('toId', $id = 10);

    $service =& $resolver->resolve($request);
    $this->assertEqual($service->getName(), '404');
  }
}

?>
