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

/**
* Compiler directive tags do not have a corresponding runtime server component,
* but they do render their contents into the compiled template.
*/
class compiler_directive_tag extends compiler_component {}
?>