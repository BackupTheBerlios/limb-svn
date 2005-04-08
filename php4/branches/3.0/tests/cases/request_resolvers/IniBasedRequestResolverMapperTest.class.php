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
require_once(LIMB_DIR . '/core/request_resolvers/IniBasedRequestResolverMapper.class.php');

class RequestResolverStub{}

class IniBasedRequestResolverMapperTest extends LimbTestCase
{
  function IniBasedRequestResolverMapperTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
  }

  function tearDown()
  {
    clearTestingIni();
  }

  function testMapFailedNoIni()
  {
    $toolkit =& Limb :: toolkit();
    $request = $toolkit->getRequest();
    $mapper = new IniBasedRequestResolverMapper();

    $this->assertNull($mapper->map($request));
  }

  function testMapFailedNoGroups()
  {
    registerTestingIni('request_resolvers.ini',
                       '');

    $toolkit =& Limb :: toolkit();
    $request = $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath('/');

    $mapper = new IniBasedRequestResolverMapper();
    $this->assertNull($mapper->map($request));
  }

  function testMapExactMatch()
  {
    registerTestingIni('request_resolvers.ini',
                       '[Main]
                        path=/
                        handle=RequestResolverStub');

    $toolkit =& Limb :: toolkit();
    $request = $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath('/');

    $mapper = new IniBasedRequestResolverMapper();

    $resolver =& $mapper->map($request);
    $this->assertIsA($resolver, 'RequestResolverStub');
  }

  function testMapNotExactMatch()
  {
    registerTestingIni('request_resolvers.ini',
                       '[Wiki]
                        path=/wiki*
                        handle=RequestResolverStub
                        [Dont match]
                        path=/wiki/*');

    $toolkit =& Limb :: toolkit();
    $request = $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath('/wiki?action=edit&id=10');

    $mapper = new IniBasedRequestResolverMapper();

    $resolver =& $mapper->map($request);
    $this->assertIsA($resolver, 'RequestResolverStub');
  }

  function testMapUseDefaultResolver()
  {
    registerTestingIni('request_resolvers.ini',
                       '
                        default_handle = RequestResolverStub
                        [Wiki]
                        path=/wiki*
                        ');

    $toolkit =& Limb :: toolkit();
    $request = $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath('/docs');

    $mapper = new IniBasedRequestResolverMapper();

    $resolver =& $mapper->map($request);
    $this->assertIsA($resolver, 'RequestResolverStub');
  }
}

?>
