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
require_once(LIMB_DIR . '/class/Object.class.php');

class ShippingConfiguration extends Object
{
  function getHash()
  {
    return md5(serialize($this->_attributes->export()));
  }

  function getZipFrom()
  {
    return $this->get('zip_from');
  }

  function setZipFrom($zip)
  {
    return $this->set('zip_from', $zip);
  }

  function getZipTo()
  {
    return $this->get('zip_to');
  }

  function setZipTo($zip)
  {
    return $this->set('zip_to', $zip);
  }

  function getCountryFrom()
  {
    return $this->get('country_from');
  }

  function setCountryFrom($country)
  {
    return $this->set('country_from', $country);
  }

  function getCountryTo()
  {
    return $this->get('country_to');
  }

  function setCountryTo($country)
  {
    return $this->set('country_to', $country);
  }

  function getDeclaredValue()
  {
    return 1*$this->get('declared_value');
  }

  function setDeclaredValue($declared_value)
  {
    return $this->set('declared_value', 1*$declared_value);
  }

  function getWeight()
  {
    return 1*$this->get('weight');
  }

  function setWeight($weight)
  {
    return $this->set('weight', 1*$weight);
  }

  function getWeightUnit()
  {
    return $this->get('weight_unit');
  }

  function setWeightUnit($unit)
  {
    return $this->set('weight_unit', $unit);
  }

  function getResidence()
  {
    return (bool)$this->get('residence');
  }

  function setResidence($status = true)
  {
    return $this->set('residence', (bool)$status);
  }
}

?>