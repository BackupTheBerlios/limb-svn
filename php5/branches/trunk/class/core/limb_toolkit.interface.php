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

interface LimbToolkit
{
  public function createDBTable($table_name);
  public function createDatasource($datasource_path);
  public function createSiteObject($site_object_path);
  public function createBehaviour($behaviour_path);
  public function createAction($action_path);
  public function getDB();
  public function getTree();
  public function getFetcher();
  public function getUser();
  public function getAuthorizer();
  public function getAuthenticator();
  public function getRequest();
  public function getResponse();
  public function getLocale();
  public function getDataspace();
  public function switchDataspace($name);
  public function setView($view);
  public function getView();
  //public function translate();
}

?> 
