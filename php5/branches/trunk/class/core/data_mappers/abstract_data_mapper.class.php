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

abstract class abstract_data_mapper
{
  //current SimpleTest has limited support for php5 features,
  //error pops up when abstract ones are partially mocked :(
  
  /*abstract*/ protected function _create_domain_object(){}
  
  /*abstract*/ protected function _get_finder(){}
  
  /*abstract*/ protected function _do_load($result_set, $domain_object){}

  public function find_by_id($id)
  {
    $result_set = $this->_get_finder()->find_by_id($id);
    
    if (!$result_set)
      return null;
    
    $domain_object = $this->_create_domain_object();
    
    $this->_do_load($result_set, $domain_object);
    
    return $domain_object;
  }  
  
  public function save($domain_object)
  {
    if($domain_object->get_id())
      $this->update($domain_object);
    else
      $this->insert($domain_object);
  }
  
  public function insert($domain_object)
  {
  }
  
  public function update($domain_object)
  {
  }
    
}

?> 
