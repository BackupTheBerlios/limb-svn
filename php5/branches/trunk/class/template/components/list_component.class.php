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
require_once(LIMB_DIR . '/class/template/component.class.php');

/**
* Represents list tags at runtime, providing an API for preparing the data set
*/
class list_component extends component
{
  /**
  * Data set to iterate over when rendering the list
  */
  public $dataset;
  /**
  * Whether to show the list seperator
  */
  public $show_separator;

  public $offset = 0;

  /**
  * Registers a dataset with the list component. The dataset must
  * implement the iterator methods defined in dataspace
  */
  public function register_dataset($dataset)
  {
    $this->dataset = $dataset;
  }

  // Temporary delegation until better solution can be found
  public function get($name, $default_value = null)
  {
    return $this->dataset->get($name, $default_value);
  }

  public function reset()
  {
    return $this->dataset->reset();
  }

  public function next()
  {
    return $this->dataset->next();
  }

  public function get_by_index_string($raw_index)
  {
    return $this->dataset->get_by_index_string($raw_index);
  }

  public function set_offset($offset)
  {
    $this->offset = $offset;
  }

  public function get_counter()
  {
    return $this->dataset->get_counter() + $this->offset + 1;
  }

  /**
  * Prepares the list for iteration, creating an empty_dataset if no
  * data set has been registered then calling the dataset reset
  * method.
  */
  public function prepare()
  {
    if (empty($this->dataset))
    {
      include_once(LIMB_DIR . '/class/core/empty_dataset.class.php');
      $this->register_dataset(new empty_dataset());
    }

    $this->show_separator = false;
  }
}

?>