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
require_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');
require_once(LIMB_DIR . '/class/template/components/form/ContainerFormElement.class.php');
require_once(LIMB_DIR . '/class/template/components/form/OptionRenderer.class.php');
require_once(LIMB_DIR . '/class/datasources/DatasourceFactory.class.php');

class OptionsFormElement extends ContainerFormElement
{
  var $default_value;

  /**
  * A associative array of choices to build the option list with
  */
  var $choice_list = array();
  /**
  * The object responsible for rendering the option tags
  */
  var $option_renderer;

  /**
  * Sets the choice list. Passed an associative array, the keys become the
  * contents of the option value attributes and the values in the array
  * become the text contents of the option tag
  */
  function setChoices($choice_list)
  {
    $this->choice_list = $choice_list;
  }

  /**
  * Sets a single option to be displayed as selected
  */
  function setSelection($selection)
  {
    $form_component = $this->findParentByClass('form_component');
    $form_component->set($this->attributes['name'], $selection);
  }

  /**
  * Sets object responsible for rendering the attributes
  */
  function setRenderer($option_renderer)
  {
    $this->option_renderer = $option_renderer;
  }

  /**
  * Renders the contents of the the select tag, option tags being built by
  * the option handler. Called from with a compiled template render function.
  */
  function renderContents()
  {
    $this->_setOptions();

    if (empty($this->option_renderer))
    {
      $this->option_renderer = new OptionRenderer();
    }

    $this->_renderOptions();
  }

  function setDefaultValue($value)
  {
    $this->default_value = $value;
  }

  function getDefaultValue()
  {
    return $this->default_value;
  }

  function getValue()
  {
    $value = parent :: getValue();

    if(!$default_value = $this->getDefaultValue())
      $default_value = reset($this->choice_list);

    if (!array_key_exists($value, $this->choice_list))
      return $default_value;
    else
      return $value;
  }

  function _setOptions()
  {
    if($this->_useIniOptions())
    {
      $this->_setOptionsFromIniFile();
    }
    elseif($this->_useStringsOptions())
    {
      $this->_setOptionsFromStringsFile();
    }
    elseif ($this->_useDatasourceOptions())
    {
      $this->_setOptionsFromDatasource();
    }
  }

  function _useIniOptions()
  {
    return $this->getAttribute('options_ini_file') &&  $this->getAttribute('use_ini');
  }

  function _useStringsOptions()
  {
    return $this->getAttribute('options_ini_file') &&  !$this->getAttribute('use_ini');
  }

  function _useDatasourceOptions()
  {
    return $this->getAttribute('options_datasource');
  }

  function _renderOptions()
  {
    $value = $this->getValue();

    foreach($this->choice_list as $key => $contents)
    {
      $this->option_renderer->renderAttribute($key, $contents, $key == $value);
    }
  }

  function _setOptionsFromIniFile()
  {
    $ini_file = $this->getAttribute('options_ini_file');
    $toolkit =& Limb :: toolkit();
    $conf =& $toolkit->getINI($ini_file . '.ini');
    $this->setChoices($conf->getOption('options', 'constants'));

    if (!$this->getDefaultValue())
      $this->setDefaultValue($conf->getOption('default_option', 'constants'));
  }

  function _setOptionsFromStringsFile()
  {
    if($locale_type = $this->getAttribute('locale_type'))
    {
      if(strtolower($locale_type) == 'content')
        $locale_constant = 'CONTENT_LOCALE_ID';
      else
        $locale_constant = 'MANAGEMENT_LOCALE_ID';
    }
    else
      $locale_constant = 'MANAGEMENT_LOCALE_ID';

    $ini_file = $this->getAttribute('options_ini_file');

    $this->setChoices(Strings :: get('options', $ini_file, constant($locale_constant)));

    $this->setDefaultValue(Strings :: get('default_option', $ini_file, constant($locale_constant)));
  }

  function _setOptionsFromDatasource()
  {
    $datasource = $this->_getDatasource();

    $this->setChoices($datasource->getOptionsArray());

    $this->setDefaultValue($datasource->getDefaultOption());
  }

  function &_getDatasource()
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->getDatasource($this->getAttribute('options_datasource'));
  }
}

?>