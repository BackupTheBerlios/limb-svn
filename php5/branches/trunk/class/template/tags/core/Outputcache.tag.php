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
  public $tag = 'core:OUTPUTCACHE';
  public $end_tag = ENDTAG_REQUIRED;
  public $tag_class = 'core_outputcache_tag';
}

registerTag(new CoreOutputcacheTagInfo());

class CoreOutputcacheTag extends ServerComponentTag
{
  public function __construct()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../../components/outputcache_component';
  }

  public function generateContents($code)
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