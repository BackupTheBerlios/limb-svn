<?php

class file_upload_required_rule extends single_field_rule
{
  function validate(&$dataspace)
  {
    $value = $dataspace->get($this->field_name);

    if (empty($value['name']))
    {
      $this->error(strings :: get('error_required', 'error'));

      //nasty hack - need to set this so single_field_rule :: validate()
      //doesn't process any more validations on this field
      $dataspace->set($this->field_name, '');
      return false;
    }
    return true;
  }
}

?>