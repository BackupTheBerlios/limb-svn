<?php
/**
* Check that a partial file upload error didn't occur. Will only work with PHP
* >= 4.2.0
*/
class file_upload_partial_rule extends single_field_rule
{
  function check($value)
  {
    if ($value['error'] == UPLOAD_ERR_PARTIAL)
    {
      $this->error('FILEUPLOAD_PARTIAL');
    }
  }
}
?>