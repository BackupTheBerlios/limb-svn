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
require_once(LIMB_DIR . '/core/error/Debug.class.php');

if(!isRegisteredResolver('dao'))
{
  include_once(LIMB_DIR . '/core/file_resolvers/PackageFileResolver.class.php');
  include_once(LIMB_DIR . '/core/file_resolvers/DAOFileResolver.class.php');
  registerFileResolver('dao', new DAOFileResolver(new PackageFileResolver()));
}

class DAOFactory
{
  var $daos = array();

  function & instance()
  {
    if (!isset($GLOBALS['DAOFactoryGlobalInstance']) || !is_a($GLOBALS['DAOFactoryGlobalInstance'], 'DAOFactory'))
      $GLOBALS['DAOFactoryGlobalInstance'] =& new DAOFactory();

    return $GLOBALS['DAOFactoryGlobalInstance'];
  }

  function & create($class_path)
  {
    $class_name = end(explode('/', $class_path));

    if(!class_exists($class_name))
    {
      $resolver =& Handle :: resolve(getFileResolver('dao'));

      if(!$full_path = $resolver->resolve($class_path))
        return null;

      include_once($full_path);
    }

    return new $class_name();
  }
}


?>
