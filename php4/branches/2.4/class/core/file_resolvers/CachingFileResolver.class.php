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
require_once(LIMB_DIR . '/class/lib/system/Fs.class.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/FileResolverDecorator.class.php');

class CachingFileResolver extends FileResolverDecorator
{
  protected $_resolved_paths = array();

  function __construct($resolver)
  {
    parent :: __construct($resolver);

    $this->_loadCache();

    //destructors are buggy!!!
    register_shutdown_function(array($this, 'SaveCache'));
  }

  public function getCacheFile()
  {
    $cache_file = VAR_DIR . '/resolvers/' . get_class($this->_resolver) . '.php';
    Fs :: mkdir(VAR_DIR . '/resolvers/');

    return $cache_file;
  }

  public function flushCache()
  {
    $this->_resolved_paths = array();
    $cache_file = $this->getCacheFile();

    if(file_exists($cache_file))
      unlink($cache_file);
  }

  protected function _loadCache()
  {
    $cache_file = $this->getCacheFile();
    if(!file_exists($cache_file))
      return;

    include($cache_file);

    if(isset($cache_resolved_paths))
      $this->_resolved_paths = $cache_resolved_paths;
    else
      $this->_resolved_paths = array();
  }

  public function saveCache()
  {
    $cache_file = $this->getCacheFile();

    $fp = fopen($cache_file, 'w+');
    if ($fp === false)
    {
      Debug :: writeError("Couldn't create cache file '{$cache_file}', perhaps wrong permissions",
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