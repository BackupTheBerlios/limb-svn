<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasource/datasource.class.php');

class version_datasource extends datasource
{
  function & get_dataset(&$counter, $params=array())
  {
    $counter = 0;

    $request = request :: instance();

    if (!$version = $request->get_attribute('version'))
      return new empty_dataset();

    if (!$node_id = $request->get_attribute('version_node_id'))
      return new empty_dataset();

    $version = (int)$version;
    $node_id = (int)$node_id;

    if(!$site_object = wrap_with_site_object(fetch_one_by_node_id($node_id)))
      return new empty_dataset();

    if(!is_subclass_of($site_object, 'content_object'))
      return new empty_dataset();

    if(($version_data = $site_object->fetch_version($version)) === false)
      return new empty_dataset();

    $result = array();

    foreach($version_data as $attrib => $value)
    {
      $data['attribute'] = $attrib;
      $data['value'] = $value;
      $result[] = $data;
    }

    return new array_dataset($result);
  }
}


?>
