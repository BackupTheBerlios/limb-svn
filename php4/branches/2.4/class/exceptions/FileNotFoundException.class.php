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

class FileNotFoundException extends LimbException
{
  var $_file_path;

  function FileNotFoundException($message, $file_path, $params = array())
  {
    $this->_file_path = $file_path;

    $params['file_path'] = $file_path;

    parent :: LimbException($message, $params);
  }

  function getFilePath()
  {
    return $this->_file_path;
  }
}

?>