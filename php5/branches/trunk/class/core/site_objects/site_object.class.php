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
require_once(LIMB_DIR . '/class/core/domain_object.class.php');

class site_object extends domain_object
{
  protected $behaviour;
  
  public function get_behaviour()
  {
    return $this->behaviour;
  }

  public function attach_behaviour($behaviour)
  {
    return $this->behaviour = $behaviour;
  }
  
  public function get_parent_node_id()
  {
    return (int)$this->get('parent_node_id');
  }

  public function set_parent_node_id($parent_node_id)
  {
    $this->set('parent_node_id', (int)$parent_node_id);
  }

  public function get_node_id()
  {
    return (int)$this->get('node_id');
  }

  public function set_node_id($node_id)
  {
    $this->set('node_id', (int)$node_id);
  }
  
  public function get_identifier()
  {
    return $this->get('identifier');
  }

  public function set_identifier($identifier)
  {
    $this->set('identifier', $identifier);
  }

  public function get_title()
  {
    return $this->get('title', '');
  }

  public function set_title($title)
  {
    $this->set('title', $title);
  }
  
  public function set_version($version)
  {
    $this->set('version', $version);
  }
  
  public function get_version()
  {
    return (int)$this->get('version');
  }
  
  public function get_locale_id()
  {
    return $this->get('locale_id');
  }   
  
  public function set_locale_id($locale_id)
  {
    $this->set('locale_id', $locale_id);
  }

  public function get_creator_id()
  {
    return (int)$this->get('creator_id');
  }   
  
  public function set_creator_id($creator_id)
  {
    $this->set('creator_id', (int)$creator_id);
  }
  
  public function get_modified_date()
  {
    return (int)$this->get('modified_date');
  }   
  
  public function set_modified_date($modified_date)
  {
    $this->set('modified_date', (int)$modified_date);
  }
  
  public function get_created_date()
  {
    return (int)$this->get('created_date');
  }   
  
  public function set_created_date($created_date)
  {
    $this->set('created_date', (int)$created_date);
  }
  
  public function get_status()//???
  {
    return (int)$this->get('status', 0);
  }   
  
  public function set_status($status)
  {
    $this->set('status', (int)$status);
  }
  
  public function get_controller()
  {
    include_once(LIMB_DIR . '/class/core/site_objects/site_object_controller.class.php');
    return new site_object_controller($this->get_behaviour());
  }
    
}

?>
