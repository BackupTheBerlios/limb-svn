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
require_once(LIMB_DIR . '/class/template/components/form/InputFormElement.class.php');

class ImageSelectComponent extends InputFormElement
{
  function initImageSelect()
  {
    if (!defined('IMAGE_SELECT_LOAD_SCRIPT'))
    {
      echo "<script type='text/javascript' src='/shared/js/image_select.js'></script>";
      define('IMAGE_SELECT_LOAD_SCRIPT',1);
    }
  }

  function renderImageSelect()
  {
    $id = $this->getAttribute('id');
    $md5id = substr(md5($id), 0, 5);

    $image_node_id = $this->getValue();

    $start_path = '';

    if($image_node_id &&  $image_data = Limb :: toolkit()->getFetcher()->fetchOneByNodeId($image_node_id))
    {
      $span_name = $image_data['identifier'];
      $start_path = '/root?action=image_select&node_id=' . $image_data['parent_node_id'];
    }
    else
      $span_name = '';

    if(!$start_path)
    {
      $start_path = $this->getAttribute('start_path');
      if(!$start_path)
        $start_path = Limb :: toolkit()->getSession()->get('limb_image_select_working_path');
      if(!$start_path)
        $start_path = '/root/images_folder';

      $start_path .= '?action=image_select';
    }

    echo "<span id='{$md5id}_name'>{$span_name}</span><br><img id='{$md5id}_img' src='/shared/images/1x1.gif'/>
      <script type='text/javascript'>
        var image_select_{$md5id};

        function init_image_select_{$md5id}()
        {
          image_select_{$md5id} = new image_select('{$id}', '{$md5id}');
          image_select_{$md5id}.set_start_path('{$start_path}');
          image_select_{$md5id}.generate();
        }

        function image_select_{$md5id}_insertImage(image)
        {
          image_select_{$md5id}.insertImage(image);
        }

        function image_select_{$md5id}_get_image()
        {
          return image_select_{$md5id}.get_image();
        }

        function image_reset_{$md5id}()
        {
          image_select_{$md5id}.id_container.value = 0;
          init_image_select_{$md5id}();
        }

        addEvent(window, 'load', init_image_select_{$md5id});
      </script>";

    echo "<br><br><input class='button' type='button' onclick='popup(\"/root/image_select?properties=0\", null, null, false, image_select_{$md5id}_insert_image, image_select_{$md5id}_get_image)' value='" . Strings :: get('select_image', 'image') . "'>";
    echo '&nbsp;';
    echo "<input class='button' type='button' onclick='image_reset_{$md5id}()' value='" . Strings :: get('reset') . "'>";
  }
}
?>