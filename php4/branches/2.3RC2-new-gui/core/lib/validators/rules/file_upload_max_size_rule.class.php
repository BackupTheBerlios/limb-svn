<?php
/**
* Check that the size of an uploaded file was not too large.
*/
class file_upload_max_size_rule extends single_field_rule
{
  var $maxsize;

  /**
  * Constructs a file_upload_max_size_rule
  */
  function file_upload_max_size_rule($fieldname, $maxsize = null)
  {
    parent :: single_field_rule($fieldname);
    $this->maxsize = $maxsize;
  }

  /**
  * Check that the uploaded file was smaller than a programmer defined size;
  * then (if PHP >= 4.2.0) the value (if any) set in the form MAX_FILE_SIZE
  * and the php.ini upload_max_filesize setting;
  */
  function check($value)
  {
    if (!is_null($this->maxsize) && $value['size'] > (int)$this->maxsize)
    {
      $this->error('FILEUPLOAD_MAX_USER_SIZE',
                   array('maxsize' =>$this->_size_to_human($this->maxsize)));
      return;
    }

    if ($value['error'] == UPLOAD_ERR_INI_SIZE || $value['error'] == UPLOAD_ERR_FORM_SIZE)
    {
      $this->error('FILEUPLOAD_MAX_SIZE');
    }
  }

  /**
  * Utility function returns readable filesizes for the error message.
  *
  * @param int $ (optional) filesize
  * @access private
  * @return string human readable filesize
  */
  function _size_to_human($filesize = 0)
  {
    if ($filesize < 1024)
    {
      $size = $filesize . "B";
    }
    else if ($filesize >= 1024 && $filesize < 1048576)
    {
      $size = sprintf("%.2fKB", $filesize / 1024);
    }
    else
    {
      $size = sprintf("%.2fMB", $filesize / 1048576);
    }
    return $size;
  }
}
?>