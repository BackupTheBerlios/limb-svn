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
require_once(LIMB_DIR . '/core/actions/FormEditSiteObjectAction.class.php');

class EditFileAction extends FormEditSiteObjectAction
{
  function _defineSiteObjectClassName()
  {
    return 'file_object';
  }

  function _defineDataspaceName()
  {
    return 'edit_file';
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

  function _defineIncreaseVersionFlag()
  {
    return false;
  }

  function _initValidator()
  {
    parent :: _initValidator();

    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/required_rule', 'title'));
  }

  function _updateObjectOperation()
  {
    if(isset($_FILES[$this->name]['tmp_name']['file']))
    {
      if(($_FILES[$this->name]['size']['file']) > ini_get('upload_max_filesize')*1024*1024)
      {
        return throw(new LimbException('uploaded file size exceeds limit'));
      }

      $toolkit =& Limb :: toolkit();
      $request =& $toolkit->getRequest();
      $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
      $datasource->setRequest($request);

      $object_data = $datasource->fetch();

      $this->object->set('media_id', $object_data['media_id']);
      $this->object->set('tmp_file_path', $_FILES[$this->name]['tmp_name']['file']);
      $this->object->set('file_name', $_FILES[$this->name]['name']['file']);
      $this->object->set('mime_type', $_FILES[$this->name]['type']['file']);
    }

    parent :: _updateObjectOperation();
  }
}

?>