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
require_once(LIMB_DIR . '/class/core/file_resolvers/FileResolver.interface.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/CachingFileResolver.class.php');

Mock :: generate('FileResolver');

class CachingFileResolverTest extends LimbTestCase
{
  var $resolver;
  var $wrapped_resolver;

  function setUp()
  {
    $this->wrapped_resolver = new MockFileResolver($this);

    $this->resolver = new CachingFileResolver($this->wrapped_resolver);
    $this->resolver->flushCache();

    $this->cache_file = $this->resolver->getCacheFile();
  }

  function tearDown()
  {
    $this->wrapped_resolver->tally();
  }

  function testResolveCachingFromWrappedResolver()
  {
    $this->wrapped_resolver->expectOnce('resolve');
    $this->wrapped_resolver->setReturnValue('resolve', 'resolved-path-to-file', array('path-to-file', array()));

    $this->assertEqual($this->resolver->resolve('path-to-file'), 'resolved-path-to-file');
  }

  function testResolveCachingFromInternalCache()
  {
    $this->wrapped_resolver->expectOnce('resolve');
    $this->wrapped_resolver->setReturnValue('resolve', 'resolved-path-to-file', array('path-to-file', array()));

    $this->resolver->resolve('path-to-file');

    $this->assertEqual($this->resolver->resolve('path-to-file'), 'resolved-path-to-file');
  }

  function testWriteToCacheOnDestroy()
  {
    $this->wrapped_resolver->setReturnValue('resolve', 'resolved-path-to-file', array('path-to-file', array()));
    $this->resolver->resolve('path-to-file');

    $this->resolver->saveCache();

    $this->assertTrue(file_exists($this->cache_file));

    include($this->cache_file);

    $this->assertEqual($cache_resolved_paths, array('path-to-file' . md5(serialize(array())) => 'resolved-path-to-file'));

    unlink($this->cache_file);
  }

  function testLoadFromCache()
  {
    $php = '
    <?php

        $cache_resolved_paths = array("path-to-file' . md5(serialize(array())). '" => "resolved-path-to-file");

    ?>';

    $fh = fopen($this->cache_file, 'w');
    fwrite($fh, $php, strlen($php));
    fclose($fh);

    $this->wrapped_resolver->expectNever('resolve');

    $local_resolver = new CachingFileResolver($this->wrapped_resolver);

    $this->assertEqual($local_resolver->resolve('path-to-file'), 'resolved-path-to-file');

    unlink($this->cache_file);
  }

}

?>