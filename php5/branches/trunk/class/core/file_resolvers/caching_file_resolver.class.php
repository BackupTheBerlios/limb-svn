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
require_once(LIMB_DIR . '/class/lib/system/fs.class.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolver_decorator.class.php');

class caching_file_resolver extends file_resolver_decorator
{
  protected $_resolved_paths = array();
  
  function __construct($resolver)
  {
    parent :: __construct($resolver);
    
    $this->_load_cache();
    
    //destructors are buggy!!!
    register_shutdown_function(array($this, 'save_cache'));
  }
  
  public function get_cache_file()
  {
    $cache_file = VAR_DIR . '/resolvers/' . get_class($this->_resolver) . '.php';    
    fs :: mkdir(VAR_DIR . '/resolvers/');  
    
    return $cache_file;
  }
    
  public function flush_cache()
  {
    $this->_resolved_paths = array();
    $cache_file = $this->get_cache_file();
    
    if(file_exists($cache_file))
      unlink($cache_file);
  }  
  
  protected function _load_cache()
  {
    $cache_file = $this->get_cache_file();
    if(!file_exists($cache_file))
      return;
    
    include($cache_file);
    
    if(isset($cache_resolved_paths))
      $this->_resolved_paths = $cache_resolved_paths;
    else
      $this->_resolved_paths = array();
  }
  
  public function save_cache()
  {
    $cache_file = $this->get_cache_file();
        
		$fp = fopen($cache_file, 'w+');
		if ($fp === false)
		{
			debug::write_error("Couldn't create cache file '{$cache_file}', perhaps wrong permissions", 
			__FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__);
			return;
		} 
  
  	fwrite($fp, "<?php\n");
  
  	fwrite($fp, '$cache_resolved_paths = ' . var_export($this->_resolved_paths, true) . ";\n");
  
  	fwrite($fp, "\n?>");
  	fclose($fp);
  }
    
  public function resolve($file_path, $params = array())
  {
    $hash = $file_path . md5(serialize($params));
    
    if(isset($this->_resolved_paths[$hash]))
      return $this->_resolved_paths[$hash];
      
    $this->_resolved_paths[$hash] = $this->_resolver->resolve($file_path, $params);
    
    return $this->_resolved_paths[$hash];
  }  

}

?>