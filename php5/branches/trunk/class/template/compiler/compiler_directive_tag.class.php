<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

/**
* Compiler directive tags do not have a corresponding runtime server component,
* but they do render their contents into the compiled template.
*/
abstract class compiler_directive_tag extends compiler_component {}
?>