<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/model/object.class.php');

class shipping_configuration extends object
{
  function get_hash()
  {
    return md5(serialize($this->_attributes->export()));
  }

  function get_zip_from()
  {
    return $this->get_attribute('zip_from');
  }

  function set_zip_from($zip)
  {
    return $this->set_attribute('zip_from', $zip);
  }

  function get_zip_to()
  {
    return $this->get_attribute('zip_to');
  }

  function set_zip_to($zip)
  {
    return $this->set_attribute('zip_to', $zip);
  }

  function get_country_from()
  {
    return $this->get_attribute('country_from');
  }

  function set_country_from($country)
  {
    return $this->set_attribute('country_from', $country);
  }

  function get_country_to()
  {
    return $this->get_attribute('country_to');
  }

  function set_country_to($country)
  {
    return $this->set_attribute('country_to', $country);
  }

  function get_declared_value()
  {
    return 1*$this->get_attribute('declared_value');
  }

  function set_declared_value($declared_value)
  {
    return $this->set_attribute('declared_value', 1*$declared_value);
  }

  function get_weight()
  {
    return 1*$this->get_attribute('weight');
  }

  function set_weight($weight)
  {
    return $this->set_attribute('weight', 1*$weight);
  }

  function get_weight_unit()
  {
    return $this->get_attribute('weight_unit');
  }

  function set_weight_unit($unit)
  {
    return $this->set_attribute('weight_unit', $unit);
  }

  function get_residence()
  {
    return (bool)$this->get_attribute('residence');
  }

  function set_residence($status = true)
  {
    return $this->set_attribute('residence', (bool)$status);
  }
}

?>