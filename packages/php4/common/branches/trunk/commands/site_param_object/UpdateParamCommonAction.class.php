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
require_once(LIMB_DIR . '/core/util/ComplexArray.class.php');
require_once(LIMB_DIR . '/core/actions/FormAction.class.php');
require_once(LIMB_DIR . '/core/SysParam.class.php');

class UpdateParamCommonAction extends FormAction
{
  var $params_type = array();

  function UpdateParamCommonAction()
  {
    parent :: FormAction();

    $this->params_type = $this->_defineParamsType();
  }

  function _defineDataspaceName()
  {
    return 'site_param_form';
  }

  function _defineParamsType()
  {
    return array(
      'site_title' => 'char',
      'contact_email' => 'char',
    );
  }

  function _initValidator()
  {
    $this->validator->addRule(array(LIMB_DIR . '/core/validators/rules/email_rule', 'contact_email'));
  }

  function _initDataspace(&$request)
  {
    $sys_param =& SysParam :: instance();

    $data = array();

    foreach($this->params_type as $param_name => $param_type)
    {
      $res = $sys_param->getParam($param_name, $param_type);

      if(catch('LimbException', $e))
      {
        $res = '';

        Debug :: writeWarning('couldnt get sys parameter',
           __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
          array(
            'param_name' => $param_name,
            'param_type' => $param_type,
          )
        );
      }
      elseif(catch('Exception', $e))
        return throw($e);

     $data[$param_name] = $res;

    $this->dataspace->import($data);
  }

  function _validPerform(&$request, &$response)
  {
    $data = $this->dataspace->export();
    $sys_param =& SysParam :: instance();

    foreach($this->params_type as $param_name => $param_type)
    {
      $sys_param->saveParam($param_name, $param_type, $data[$param_name]);

      if(catch('SQLException', $e))
        return throw($e);
      elseif(catch('LimbException', $e))
      {
        Debug :: writeWarning('couldnt save sys parameter',
           __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
          array(
            'param_name' => $param_name,
            'param_type' => $param_type,
          )
        );
      }

    }

    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);
  }
}
?>