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
require_once(LIMB_DIR . '/class/template/compiler/CompilerDirectiveTag.class.php');

/**
* The root compile time component in the template hierarchy. Used to generate
* the correct reference PHP code like $dataspace->...
*/
class RootCompilerComponent extends CompilerDirectiveTag
{
  /**
  * Calls the parent pre_generate() method then writes
  * "$dataspace->prepare();" to the compiled template.
  */
  public function preGenerate($code)
  {
    parent::preGenerate($code);

    if($this->isDebugEnabled())
    {
      $code->writeHtml("<div class='debug-tmpl-main'>");

      $this->_generateDebugEditorLinkHtml($code, $this->source_file);
    }
  }

  public function postGenerate($code)
  {
    if($this->isDebugEnabled())
    {
      $code->writeHtml('</div>');
    }

    parent :: postGenerate($code);
  }

  /**
  * Returns the base for building the PHP runtime component reference string
  */
  public function getComponentRefCode()
  {
    return '$dataspace';
  }

  /**
  * Returns $dataspace
  */
  public function getDataspaceRefCode()
  {
    return '$dataspace';
  }

  /**
  * Returns this instance of root_compiler_component
  */
  public function getDataspace()
  {
    return $this;
  }
}

?>
