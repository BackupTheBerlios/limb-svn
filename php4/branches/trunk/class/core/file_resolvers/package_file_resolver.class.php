<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class package_file_resolver
{
  var $_packages = array();
  var $_resolved_file_paths = array();
    
  function resolve($file_path)
  {
    if (isset($this->_resolved_file_paths[$file_path]))
      return $this->_resolved_file_paths[$file_path];
  
    $resolved_file_path = $this->_do_resolve($file_path);
    
    $this->_resolved_file_paths[$file_path] = $resolved_file_path;
    
    return $resolved_file_path;
  }
  
  function _do_resolve($file_path)
  {
    return $this->_find_file_in_packages($file_path);
  }
  
/*  function _write_cache()
  {
		if (!count($this->_resolved_file_paths))
		  return;
    
    $cache_file = VAR_DIR . 'cache/' . get_class($this) . '.php';
    
		$fp = @fopen($cache_file, 'w+');
		if ($fp === false)
		{
			debug::write_error("Couldn't create cache file '{$cache_file}', perhaps wrong permissions", __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__);
			return;
		} 

		fwrite($fp, "<?php\n");
		fwrite($fp, '$resolved_file_paths = ' . var_export($this->_resolved_file_paths, true) . ";\n");

		fwrite($fp, "\n?>");
		fclose($fp);
  }*/
  
  function _find_file_in_packages($file_path)
  {
    $packages = $this->get_packages();
    
    foreach($packages as $package)
    {
      if (!isset($package['path']))
        continue;
              
      $resolved_file_path = $package['path'] . $file_path;
      if (file_exists($resolved_file_path))
        return $resolved_file_path;
    }
    
    return false;
  }
  
  function get_packages()
  {
    if(!$this->_packages)
      $this->_load_packages();
    
    return $this->_packages;
  }

  function _load_packages()
  {
    include_once(LIMB_DIR . '/class/lib/util/ini.class.php');
    
    $ini =& get_ini('packages.ini');
    $this->_packages = array();
    
    $groups = $ini->get_all();

    foreach($groups as $group => $data)
    {
      $data['path'] = $this->_parse_path($data['path']);
      
      if(strpos($group, 'package') === 0)
        $this->_packages[] = $data;
    }
  }
  
  function _parse_path($path)
  {
    return preg_replace('~\{([^\}]+)\}~e', "constant('\\1')", $path);
  }  
}

?>