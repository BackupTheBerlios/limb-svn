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

class FileSelectComponent extends InputFormElement
{
  public function initFileSelect()
  {
    if (!defined('FILE_SELECT_LOAD_SCRIPT'))
    {
      echo "<script type='text/javascript' src='/shared/js/file_select.js'></script>";
      define('FILE_SELECT_LOAD_SCRIPT',1);
    }
  }

  public function renderFileSelect()
  {
    $id = $this->getAttribute('id');
    $md5id = substr(md5($id), 0, 5);

    $file_node_id = $this->getValue();

    if($file_node_id &&  $file_data = Limb :: toolkit()->getFetcher()->fetchOneByNodeId($file_node_id))
    {
      $span_name = $file_data['identifier'];
      $span_description = $file_data['description'];
      $span_size = $file_data['size'];
      $span_mime = $file_data['mime_type'];
    }
    else
    {
      $span_name = '';
      $span_description = '';
      $span_size = '';
      $span_mime = '';
    }

    echo "<span id='{$md5id}_span_empty'><img src='/shared/images/no_img.gif'></span>
          <span id='{$md5id}_span_content'>
            <a id='{$md5id}_href' href='#'><img id='{$md5id}_img' align='center' src='/shared/images/1x1.gif'/>&nbsp;<span id='{$md5id}_name'>{$span_name}</span></a><br>
            <span id='{$md5id}_description'>{$span_description}</span><br>
            size:&nbsp;<span id='{$md5id}_size'>{$span_size}</span>&nbsp;bytes&nbsp;<br>
            mime-type:<span id='{$md5id}_mime'>{$span_mime}</span>
          </span><br><br>";

    echo "<script type='text/javascript'>
          var file_select_{$md5id};

          function init_file_select_{$md5id}()
          {
            file_select_{$md5id} = new file_select('{$id}', '{$md5id}');
            file_select_{$md5id}.generate();
          }

          function file_select_{$md5id}_insertFile(file)
          {
            file_select_{$md5id}.insertFile(file);
          }

          function file_select_{$md5id}_get_file()
          {
            return file_select_{$md5id}.get_file();
          }

          function file_reset_{$md5id}()
          {
            file_select_{$md5id}.id_container.value = 0;
            init_file_select_{$md5id}();
          }

          addEvent(window, 'load', init_file_select_{$md5id});
        </script>";

    echo "<input class='button' type='button' onclick='popup(\"/root/file_select\", null, null, false, file_select_{$md5id}_insert_file, file_select_{$md5id}_get_file)' value='" . Strings :: get('select_file', 'file') . "'>";
    echo '&nbsp;';
    echo "<input class='button' type='button' onclick='file_reset_{$md5id}()' value='" . Strings :: get('reset') . "'>";
  }
}
?>