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
require_once(LIMB_DIR . '/class/template/Component.class.php');

/**
* Represents list tags at runtime, providing an API for preparing the data set
*/
class ListComponent extends Component
{
  /**
  * Data set to iterate over when rendering the list
  */
  var $dataset;
  /**
  * Whether to show the list seperator
  */
  var $show_separator;

  var $offset = 0;

  /**
  * Registers a dataset with the list component. The dataset must
  * implement the iterator methods defined in dataspace
  */
  function registerDataset($dataset)
  {
    $this->dataset = $dataset;
  }

  // Temporary delegation until better solution can be found
  function get($name, $default_value = null)
  {
    return $this->dataset->get($name, $default_value);
  }

  function reset()
  {
    return $this->dataset->reset();
  }

  function next()
  {
    return $this->dataset->next();
  }

  function getByIndexString($raw_index)
  {
    return $this->dataset->getByIndexString($raw_index);
  }

  function setOffset($offset)
  {
    $this->offset = $offset;
  }

  function getCounter()
  {
    return $this->dataset->getCounter() + $this->offset + 1;
  }

  /**
  * Prepares the list for iteration, creating an empty_dataset if no
  * data set has been registered then calling the dataset reset
  * method.
  */
  function prepare()
  {
    if (empty($this->dataset))
    {
      include_once(LIMB_DIR . '/class/core/EmptyDataset.class.php');
      $this->registerDataset(new EmptyDataset());
    }

    $this->show_separator = false;
  }
}

?>