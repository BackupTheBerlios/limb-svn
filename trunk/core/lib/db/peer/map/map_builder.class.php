<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class map_builder
{
  /**
   * Build up the database mapping.
   * @return void
   * @throws Exception Couldn't build mapping.
   */
  function do_build()
  {
  	error('abstract function', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
  }

  /**
   * Tells us if the database mapping is built so that we can avoid
   * re-building it repeatedly.
   *
   * @return boolean Whether the database_map is built.
   */
  function is_built()
  {
    error('abstract function', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
  }

  /**
   * Gets the database mapping this map builder built.
   *
   * @return database_map A database_map.
   */
  function get_database_map()
  {
    error('abstract function', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
  }
}
