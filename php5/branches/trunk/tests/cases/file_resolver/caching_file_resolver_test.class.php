<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolver.interface.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/caching_file_resolver.class.php');

Mock::generate('file_resolver');

class caching_file_resolver_test extends LimbTestCase
{
  var $resolver;
  var $wrapped_resolver;  
  
  function setUp()
  {
    $this->wrapped_resolver = new Mockfile_resolver($this);
    
    $this->resolver = new caching_file_resolver($this->wrapped_resolver);
    $this->resolver->flush_cache();
    
    $this->cache_file = $this->resolver->get_cache_file();
  }
  
  function tearDown()
  {
    $this->wrapped_resolver->tally();
  }  
  
  function test_resolve_caching_from_wrapped_resolver()
  {
    $this->wrapped_resolver->expectOnce('resolve');
    $this->wrapped_resolver->setReturnValue('resolve', 'resolved-path-to-file', array('path-to-file', array()));
    
    $this->assertEqual($this->resolver->resolve('path-to-file'), 'resolved-path-to-file');
  }

  function test_resolve_caching_from_internal_cache()
  {
    $this->wrapped_resolver->expectOnce('resolve');
    $this->wrapped_resolver->setReturnValue('resolve', 'resolved-path-to-file', array('path-to-file', array()));
    
    $this->resolver->resolve('path-to-file');
    
    $this->assertEqual($this->resolver->resolve('path-to-file'), 'resolved-path-to-file');
  }

  function test_write_to_cache_on_destroy()
  {
    $this->wrapped_resolver->setReturnValue('resolve', 'resolved-path-to-file', array('path-to-file', array()));
    $this->resolver->resolve('path-to-file');
    
    $this->resolver->save_cache();
    
    $this->assertTrue(file_exists($this->cache_file));
    
    include($this->cache_file);
    
    $this->assertEqual($cache_resolved_paths, array('path-to-file' => 'resolved-path-to-file'));
    
    unlink($this->cache_file);
  }
  
  function test_load_from_cache()
  {
    $php = '
    <?php
    
        $cache_resolved_paths = array("path-to-file" => "resolved-path-to-file");
        
    ?>';
    
    file_put_contents($this->cache_file, $php);

    $local_resolver = new caching_file_resolver($this->wrapped_resolver);

    $this->wrapped_resolver->expectNever('resolve');
    
    $this->assertEqual($local_resolver->resolve('path-to-file'), 'resolved-path-to-file');
    
    unlink($this->cache_file);
  }
  
}

?>