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
require_once(LIMB_DIR . '/class/core/Object.class.php');

class ShippingConfiguration extends Object
{
  public function getHash()
  {
    return md5(serialize($this->_attributes->export()));
  }

  public function getZipFrom()
  {
    return $this->get('zip_from');
  }

  public function setZipFrom($zip)
  {
    return $this->set('zip_from', $zip);
  }

  public function getZipTo()
  {
    return $this->get('zip_to');
  }

  public function setZipTo($zip)
  {
    return $this->set('zip_to', $zip);
  }

  public function getCountryFrom()
  {
    return $this->get('country_from');
  }

  public function setCountryFrom($country)
  {
    return $this->set('country_from', $country);
  }

  public function getCountryTo()
  {
    return $this->get('country_to');
  }

  public function setCountryTo($country)
  {
    return $this->set('country_to', $country);
  }

  public function getDeclaredValue()
  {
    return 1*$this->get('declared_value');
  }

  public function setDeclaredValue($declared_value)
  {
    return $this->set('declared_value', 1*$declared_value);
  }

  public function getWeight()
  {
    return 1*$this->get('weight');
  }

  public function setWeight($weight)
  {
    return $this->set('weight', 1*$weight);
  }

  public function getWeightUnit()
  {
    return $this->get('weight_unit');
  }

  public function setWeightUnit($unit)
  {
    return $this->set('weight_unit', $unit);
  }

  public function getResidence()
  {
    return (bool)$this->get('residence');
  }

  public function setResidence($status = true)
  {
    return $this->set('residence', (bool)$status);
  }
}

?>