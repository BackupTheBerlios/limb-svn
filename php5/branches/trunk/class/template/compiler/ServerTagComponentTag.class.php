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
* Server tag component tags are server_component_tags which also correspond to
* an HTML tag. Makes it easier to implement instead of extending from the
* server_component_tag class
*/
abstract class ServerTagComponentTag extends ServerComponentTag
{
  /**
  * Returns the XML tag name
  */
  public function getRenderedTag()
  {
    return $this->tag;
  }

  /**
  * Adds any additional XML attributes
  */
  public function generateExtraAttributes($code)
  {
  }

  /**
  * Calls the parent pre_generate() method then writes the XML tag name
  * plus a PHP string which renders the attributes from the runtime
  * component.
  */
  public function preGenerate($code)
  {
    parent::preGenerate($code);
    $code->writeHtml('<' . $this->getRenderedTag());
    $code->writePhp($this->getComponentRefCode() . '->render_attributes();');
    $this->generateExtraAttributes($code);
    $code->writeHtml('>');
  }

  /**
  * Writes the closing tag string to the compiled template
  */
  public function postGenerate($code)
  {
    if ($this->has_closing_tag)
    {
      $code->writeHtml('</' . $this->getRenderedTag() . '>');
    }
    parent::postGenerate($code);
  }

  /**
  * Writes the compiled template constructor from the runtime component,
  * assigning the attributes found at compile time to the runtime component
  * via a serialized string
  */
  public function generateConstructor($code)
  {
    parent::generateConstructor($code);
    $code->writePhp($this->getComponentRefCode() . '->attributes = ' . var_export($this->attributes, true) . ';');
  }
}

?>