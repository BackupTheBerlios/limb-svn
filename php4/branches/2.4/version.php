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

/**
 * This file is subject to change for each release
 */

/**
 * Naming, version & release date
 */
define('LIMB_NAME', 'LIMB');

/**
 * Additional software subname string
 */
define('LIMB_SUBNAME', 'Lithesome Interactive Media Builder');

/**
 * Major software version
 */
define('LIMB_VERSION_MAJOR', '2');

/**
 * Minor software version
 */
define('LIMB_VERSION_MINOR', '9');

/**
 * Micro software version
 */
define('LIMB_VERSION_MICRO', '');

/**
 * Software version patch
 */
define('LIMB_VERSION_PATCH', '-alpha');

/**
 * Software release version
 */
define('LIMB_RELEASE_NAME', '');

/**
 * Software build (full) date
 */
define('LIMB_VERSION_DATE', 'August 10, 2004');


// --- Do not change from here ----------------------------------------

/**
 * Software build (unix timestamp) date
 */
define('LIMB_VERSION_STAMP', strtotime(LIMB_VERSION_DATE));

/**
 * Complete software version string
 */
define('LIMB_VERSION', LIMB_VERSION_MAJOR . '.'
                         . LIMB_VERSION_MINOR . '.'
                         . LIMB_VERSION_MICRO
                         . LIMB_VERSION_PATCH);

/**
 * Complete software name string
 */
define('LIMB_FULL_NAME', LIMB_NAME . ' ' . LIMB_VERSION);

/**
 * The URL of the home of this software
 */
define('LIMB_HOME', 'http://limb-project.com');

?>
