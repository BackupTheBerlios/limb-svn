<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: metadata_component.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/template/component.class.php');

class action_icon_component extends component
{

  function get_icon_path()
  {
    $variation = '';
    if($variation = $this->get('variation'))
      $variation = $this->get('variation') . '/';

    $image_name = $this->get('identifier');
    if(empty($image_name))
      $image_name = 'default';

    if(file_exists(PROJECT_DIR  . '/design/main/images/actions/' . $variation. $image_name . '.gif'))
      $result = '/design/main/images/actions/' . $variation . $image_name . '.gif';
    else if(file_exists(SHARED_DIR . '/images/actions/' . $variation . $image_name . '.gif'))
      $result = SHARED_IMG_URL . '/actions/' . $variation . $image_name . '.gif';
    else
      $result = SHARED_IMG_URL . '/actions/default.gif';


    echo $result;
  }
}

?>