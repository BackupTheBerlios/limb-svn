<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbPagerTagTest.class.php 1055 2005-01-24 14:35:50Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');
require_once(WACT_ROOT . '/template/components/page/pager.inc.php');

Mock :: generate('PageNavigatorComponent');

class LimbListTagTestCase extends LimbTestCase
{
  var $names = array();

  function LimbListTagTestCase()
  {
    parent :: LimbTestCase('limb list tags case');

    $this->names = array(array('name' => 'Alex'),
                         array('name' => 'Serega'),
                         array('name' => 'Pavel'),
                         array('name' => 'John'));
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testLimbSeparatorDefault()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$name}<limb:list:SEPARATOR>|</limb:list:SEPARATOR></list:ITEM>'.
                '</list:LIST>';

    RegisterTestingTemplate('/limb/list_separator_default.html', $template);

    $page =& new Template('/limb/list_separator_default.html');

    $list =& $page->getChild('test');
    $list->registerDataSet(new PagedArrayDataSet($this->names));

    $this->assertEqual($page->capture(), 'Alex|Serega|Pavel|John');
  }

  function testLimbSeparatorWithDefinedStep()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$name}<limb:list:SEPARATOR step="2">|</limb:list:SEPARATOR></list:ITEM>'.
                '</list:LIST>';

    RegisterTestingTemplate('/limb/list_separator_defined_step.html', $template);

    $page =& new Template('/limb/list_separator_defined_step.html');

    $list =& $page->getChild('test');
    $list->registerDataSet(new PagedArrayDataSet($this->names));

    $this->assertEqual($page->capture(), 'AlexSerega|PavelJohn');
  }

  function testLimbRowNumber()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$LimbListRowNumber}:{$name}</list:ITEM>'.
                '</list:LIST>';

    RegisterTestingTemplate('/limb/list_row_number_no_pager.html', $template);

    $page =& new Template('/limb/list_row_number_no_pager.html');

    $list =& $page->getChild('test');
    $list->registerDataSet(new PagedArrayDataSet($this->names));

    $this->assertEqual($page->capture(), '1:Alex2:Serega3:Pavel4:John');
  }

  function testLimbRowNumberDataSetIsPaginated()
  {
    $template = '<list:LIST id="test">'.
                '<list:ITEM>{$LimbListRowNumber}:{$name}</list:ITEM>'.
                '</list:LIST>';

    RegisterTestingTemplate('/limb/list_row_number_pager.html', $template);

    $page =& new Template('/limb/list_row_number_pager.html');

    $list =& $page->getChild('test');

    $dataset = new PagedArrayDataSet($this->names);
    $pager = new MockPageNavigatorComponent($this);
    $pager->setReturnValue('getStartingItem', 3);
    $pager->setReturnValue('getItemsPerPage', 4);
    $dataset->paginate($pager);

    $list->registerDataSet($dataset);

    $this->assertEqual($page->capture(), '3:Pavel4:John');

    $pager->tally();
  }

}
?>

