<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
 
class FileNotFoundException extends LimbException 
{    
  protected $_file_path;
  
  public function __construct($message, $file_path, $params = array())
  {
    $this->_file_path = $file_path;
    
    $params['file_path'] = $file_path;
        
    parent::__construct($message, $params);
  }
        
  public function getFilePath()
  {
    return $this->_file_path;
  } 
}

?>