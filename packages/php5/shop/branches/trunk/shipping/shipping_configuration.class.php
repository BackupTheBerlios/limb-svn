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
require_once(LIMB_DIR . '/class/core/object.class.php');

class shipping_configuration extends object
{
  public function get_hash()
  {
    return md5(serialize($this->_attributes->export()));
  }

  public function get_zip_from()
  {
    return $this->get('zip_from');
  }

  public function set_zip_from($zip)
  {
    return $this->set('zip_from', $zip);
  }

  public function get_zip_to()
  {
    return $this->get('zip_to');
  }

  public function set_zip_to($zip)
  {
    return $this->set('zip_to', $zip);
  }

  public function get_country_from()
  {
    return $this->get('country_from');
  }

  public function set_country_from($country)
  {
    return $this->set('country_from', $country);
  }

  public function get_country_to()
  {
    return $this->get('country_to');
  }

  public function set_country_to($country)
  {
    return $this->set('country_to', $country);
  }

  public function get_declared_value()
  {
    return 1*$this->get('declared_value');
  }

  public function set_declared_value($declared_value)
  {
    return $this->set('declared_value', 1*$declared_value);
  }

  public function get_weight()
  {
    return 1*$this->get('weight');
  }

  public function set_weight($weight)
  {
    return $this->set('weight', 1*$weight);
  }

  public function get_weight_unit()
  {
    return $this->get('weight_unit');
  }

  public function set_weight_unit($unit)
  {
    return $this->set('weight_unit', $unit);
  }

  public function get_residence()
  {
    return (bool)$this->get('residence');
  }

  public function set_residence($status = true)
  {
    return $this->set('residence', (bool)$status);
  }
}

?>