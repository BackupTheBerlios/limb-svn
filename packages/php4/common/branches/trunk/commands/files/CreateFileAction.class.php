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
require_once(LIMB_DIR . '/core/actions/FormCreateSiteObjectAction.class.php');

class CreateFileAction extends FormCreateSiteObjectAction
{
  function _defineSiteObjectClassName()
  {
    return 'file_object';
  }

  function _defineDataspaceName()
  {
    return 'create_file';
  }

  function _defineDatamap()
  {
    return ComplexArray :: array_merge(
        parent :: _defineDatamap(),
        array(
          'description' => 'description',
        )
    );
  }

  function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/required_rule', 'title'));
  }

  function _createObjectOperation()
  {
    if(isset($_FILES[$this->name]['tmp_name']['file']))
    {
      if(($_FILES[$this->name]['size']['file']) > ini_get('upload_max_filesize')*1024*1024)
      {
        MessageBox :: writeWarning('uploaded file size exceeds limit');
        return false;
      }

      $this->object->set('tmp_file_path', $_FILES[$this->name]['tmp_name']['file']);
      $this->object->set('file_name', $_FILES[$this->name]['name']['file']);
      $this->object->set('mime_type', $_FILES[$this->name]['type']['file']);
    }

    return parent :: _createObjectOperation();
  }
}

?>