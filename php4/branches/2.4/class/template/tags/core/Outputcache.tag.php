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
class CoreOutputcacheTagInfo
{
  var $tag = 'core:OUTPUTCACHE';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'core_outputcache_tag';
}

registerTag(new CoreOutputcacheTagInfo());

class CoreOutputcacheTag extends ServerComponentTag
{
  function CoreOutputcacheTag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/outputcache_component';
  }

  function generateContents($code)
  {
    $v = '$' . $code->getTempVariable();

    $code->writePhp($this->getComponentRefCode() . '->prepare();');
    $code->writePhp('if (!(' . $v . ' = ' . $this->getComponentRefCode() . '->get())) {');
    $code->writePhp('ob_start();');

    parent::generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->write(ob_get_contents());ob_end_flush();');
    $code->writePhp('}');
    $code->writePhp('else echo ' . $v . ';');
  }
}

?>