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
require_once(LIMB_DIR . '/class/actions/FormAction.class.php');

class RegisterNewObjectAction extends FormAction
{
  function _defineDataspaceName()
  {
    return 'register_new_object';
  }

  function _initValidator()
  {
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'class_name'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'identifier'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'parent_path'));
    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/tree_path_rule', 'parent_path'));

    if($path = $this->dataspace->get('parent_path'))
    {
      $toolkit =& Limb :: toolkit();
      $tree =& $toolkit->getTree();
      if($node = $tree->getNodeByPath($path))
        $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/tree_identifier_rule', 'identifier', $node['id']));
    }

    $this->validator->addRule(array(LIMB_DIR . '/class/validators/rules/required_rule', 'title'));
  }

  function _validPerform(&$request, &$response)
  {
    $params = array();

    $params['identifier'] = $this->dataspace->get('identifier');
    $params['parent_path'] = $this->dataspace->get('parent_path');
    $params['class'] = $this->dataspace->get('class_name');
    $params['title'] = $this->dataspace->get('title');

    $toolkit =& Limb :: toolkit();
    $object =& $toolkit->createSiteObject($params['Class']);

    $datasource =& $toolkit->getDatasource('SingleObjectDatasource');
    $datasource->setPath($params['parent_path']);

    $is_root = false;
    if(!$parent_data = $datasource->fetch())
    {
      if ($params['parent_path'] == '/')
        $is_root = true;
      else
      {
        MessageBox :: writeNotice('parent wasn\'t retrieved by path ' . $params['parent_path']);
        $request->setStatus(Request :: STATUS_FAILURE);
        return;
      }
    }

    if (!$is_root)
      $params['parent_node_id'] = $parent_data['node_id'];
    else
      $params['parent_node_id'] = 0;

    $object->merge($params);

    $object->create($is_root);

    if(catch($e, 'LimbException'))
    {
      MessageBox :: writeNotice('object wasn\'t registered!');
      $request->setStatus(Request :: STATUS_FAILURE);
      return;
    }
    elseif(catch($e, 'Exception'))
      return throw($e);

    if (!$is_root)
    {
      $parent_object =& $toolkit->createSiteObject($parent_data['ClassName']);
      $parent_object->merge($parent_data);

      $controller =& $parent_object->getController();
      $action = $controller->determineAction();

      $access_policy = new AccessPolicy();
      $access_policy->saveNewObjectAccess($object, $parent_object, $action);
    }

    $request->setStatus(Request :: STATUS_FORM_SUBMITTED);

    if($request->hasAttribute('popup'))
      $response->write(closePopupResponse($request));
  }
}

?>