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
require_once(LIMB_DIR . 'class/lib/error/error.inc.php');

$LIMB_FILE_RESOLVERS = array();

function get_file_resolver($resolver_name)
{
  global $LIMB_FILE_RESOLVERS;
  if(isset($LIMB_FILE_RESOLVERS[$resolver_name]))
    return $LIMB_FILE_RESOLVERS[$resolver_name];
  else
    error('unknown file resolver',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
		array('resolver' => $resolver_name));
}

function register_file_resolver($resolver_name, $resolver)
{
  global $LIMB_FILE_RESOLVERS;

  $LIMB_FILE_RESOLVERS[$resolver_name] = $resolver;
}

function is_registered_resolver($resolver_name)
{
  global $LIMB_FILE_RESOLVERS;
  
  return isset($LIMB_FILE_RESOLVERS[$resolver_name]);
}

?>