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

$LIMB_FILE_RESOLVERS = array();

function & getFileResolversList()
{
  global $LIMB_FILE_RESOLVERS;
  return $LIMB_FILE_RESOLVERS;
}

function & getFileResolver($resolver_name)
{
  global $LIMB_FILE_RESOLVERS;
  if(isset($LIMB_FILE_RESOLVERS[$resolver_name]))
    return $LIMB_FILE_RESOLVERS[$resolver_name];
  else
    throw new LimbException('unknown file resolver',
      array('resolver' => $resolver_name));
}

function registerFileResolver($resolver_name, $resolver)
{
  global $LIMB_FILE_RESOLVERS;

  $LIMB_FILE_RESOLVERS[$resolver_name] = $resolver;
}

function isRegisteredResolver($resolver_name)
{
  global $LIMB_FILE_RESOLVERS;

  return isset($LIMB_FILE_RESOLVERS[$resolver_name]);
}

?>