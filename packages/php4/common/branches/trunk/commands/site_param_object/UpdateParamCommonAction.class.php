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
require_once(LIMB_DIR . '/class/lib/util/ComplexArray.class.php');
require_once(LIMB_DIR . '/class/core/actions/FormAction.class.php');
require_once(LIMB_DIR . '/class/core/SysParam.class.php');

class UpdateParamCommonAction extends FormAction
{
  protected $params_type = array();

  function __construct()
  {
    parent :: __construct();

    $this->params_type = $this->_defineParamsType();
  }

  protected function _defineDataspaceName()
  {
    return 'site_param_form';
  }

  protected function _defineParamsType()
  {
    return array(
      'site_title' => 'char',
      'contact_email' => 'char',
    );
  }

  protected function _initValidator()
  {
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/email_rule', 'contact_email'));
  }

  protected function _initDataspace($request)
  {
    $sys_param = SysParam :: instance();

    $data = array();

    foreach($this->params_type as $param_name => $param_type)
    {
      try
      {
        $data[$param_name] = $sys_param->getParam($param_name, $param_type);
      }
      catch(LimbException $e)
      {
        $data[$param_name] = '';

        Debug :: writeWarning('couldnt get sys parameter',
           __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
          array(
            'param_name' => $param_name,
            'param_type' => $param_type,
          )
        );

      }
    }

    $this->dataspace->import($data);
  }

  protected function _validPerform($request, $response)
  {
    $data = $this->dataspace->export();
    $sys_param = SysParam :: instance();

    foreach($this->params_type as $param_name => $param_type)
    {
      try
      {
        $sys_param->saveParam($param_name, $param_type, $data[$param_name]);
      }
      catch(SQLException $e)
      {
        throw $e;
      }
      catch(LimbException $e)
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