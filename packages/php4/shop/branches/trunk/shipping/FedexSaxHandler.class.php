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

class FedexSaxHandler
{
  protected $tags_list = array();
  protected $options = array();
  protected $total_options = 0;
  protected $services_began = false;

  protected $service_name_began = false;
  protected $service_description_began = false;
  protected $service_price_began = false;

  protected $service_name_expected = false;
  protected $service_description_expected = false;
  protected $service_price_expected = false;

  public function openHandler($parser, $name, $attrs)
  {
    if($this->service_name_expected ||  $this->service_description_expected ||  $this->service_price_expected)
      $this->tags_list[] = strtolower($name);

    if($this->service_name_began)
    {
      $this->options[$this->total_options]['name'] .= '<' . $name;
      $this->options[$this->total_options]['name'] .= $this->writeAttributes($attrs);
      $this->options[$this->total_options]['name'] .= '>';
    }

    if($this->service_description_began)
    {
      $this->options[$this->total_options]['description'] .= '<' . $name;
      $this->options[$this->total_options]['description'] .= $this->writeAttributes($attrs);
      $this->options[$this->total_options]['description'] .= '>';
    }

    if($this->service_price_began)
    {
      $this->options[$this->total_options]['price'] .= '<' . $name;
      $this->options[$this->total_options]['price'] .= $this->writeAttributes($attrs);
      $this->options[$this->total_options]['price'] .= '>';
    }

    if($this->services_began)
    {
      if($this->service_name_expected &&  $this->tags_list == array('td', 'table', 'tr', 'td'))
        $this->service_name_began = true;

      if($this->service_description_expected &&  $this->tags_list == array('td', 'table', 'tr', 'td'))
        $this->service_description_began = true;

      if($this->service_price_expected &&  $this->tags_list == array('td', 'table', 'tr', 'td'))
      {
        $this->service_price_began = true;
      }
    }
  }

  public function closeHandler($parser, $name)
  {
    $tag = strtolower($name);

    if($this->service_name_began)
    {
      if($tag == 'td')
      {
        $this->service_name_began = false;
        $this->service_name_expected = false;
        return;
      }

      $this->options[$this->total_options]['name'] .= '</' . $name . '>';
    }

    if($this->service_description_began)
    {
      if($tag == 'td')
      {
        $this->service_description_began = false;
        $this->service_description_expected = false;
        return;
      }

      $this->options[$this->total_options]['description'] .= '</' . $name . '>';
    }

    if($this->service_price_began)
    {
      if($tag == 'td')
      {
        $this->service_price_began = false;
        $this->service_price_expected = false;
        return;
      }

      $this->options[$this->total_options]['price'] .= '</' . $name . '>';
    }
  }

  public function dataHandler($parser, $data)
  {
    if($this->service_name_began)
    {
      $this->options[$this->total_options]['name'] .= trim($data);
    }

    if($this->service_description_began)
    {
      $this->options[$this->total_options]['description'] .= trim($data);
    }

    if($this->service_price_began)
    {
      $this->options[$this->total_options]['price'] .= trim($data);
    }

  }

  public function escapeHandler($parser, $data)
  {
    switch(trim($data))
    {
      case '-- TABLE:  Begin Services. --':
        $this->services_began = true;
      break;

      case '-- column:  Service --':

        if(!$this->services_began)
          break;

        $this->total_options++;

        $this->tags_list = array();
        $this->service_name_expected = true;

        $this->options[$this->total_options]['name'] = '';
        $this->options[$this->total_options]['description'] = '';
        $this->options[$this->total_options]['price'] = '';

      break;

      case '-- column:  Delivery Time --':

        if(!$this->services_began)
          break;

        $this->tags_list = array();
        $this->service_description_expected = true;

      break;

      case '-- column:  Rate (US$) --':

        if(!$this->services_began)
          break;

        $this->tags_list = array();
        $this->service_price_expected = true;

      break;
    }
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function writeAttributes($attributes)
  {
    if(!is_array($attributes))
      return '';

    $attrs = '';

    foreach ($attributes as $name => $value)
    {
      $attrs .= ' ' . $name . '="' . $value . '"';
    }

    return $attrs;
  }
}
?>