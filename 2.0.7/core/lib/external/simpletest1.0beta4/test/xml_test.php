<?php
    // $Id: xml_test.php,v 1.9 2004/01/05 20:22:10 lastcraft Exp $
    
    if (!defined("SIMPLE_TEST")) {
        define("SIMPLE_TEST", "../");
    }
    require_once(SIMPLE_TEST . 'xml.php');
    
    Mock::generate('SimpleRunner');
    
    class TestOfNestingTags extends UnitTestCase {
        function TestOfNestingTags() {
            $this->UnitTestCase();
        }
        function testGroupSize() {
            $nesting = new NestingGroupTag(array('SIZE' => 2));
            $this->assertEqual($nesting->getSize(), 2);
        }
    }
    
    class TestOfXmlStructureParsing extends UnitTestCase {
        function TestOfXmlStructureParsing() {
            $this->UnitTestCase();
        }
        function testValidXml() {
            $listener = &new MockSimpleRunner($this);
            $listener->expectNever('paintGroupStart');
            $listener->expectNever('paintGroupEnd');
            $listener->expectNever('paintCaseStart');
            $listener->expectNever('paintCaseEnd');
            $parser = &new SimpleTestXmlParser($listener);
            $this->assertTrue($parser->parse("<?xml version=\"1.0\"?>\n"));
            $this->assertTrue($parser->parse("<run>\n"));
            $this->assertTrue($parser->parse("</run>\n"));
        }
        function testEmptyGroup() {
            $listener = &new MockSimpleRunner($this);
            $listener->expectOnce('paintGroupStart', array('a_group', 7));
            $listener->expectOnce('paintGroupEnd', array('a_group'));
            $parser = &new SimpleTestXmlParser($listener);
            $parser->parse("<?xml version=\"1.0\"?>\n");
            $parser->parse("<run>\n");
            $this->assertTrue($parser->parse("<group size=\"7\">\n"));
            $this->assertTrue($parser->parse("<name>a_group</name>\n"));
            $this->assertTrue($parser->parse("</group>\n"));
            $parser->parse("</run>\n");
            $listener->tally();
        }
        function testEmptyCase() {
            $listener = &new MockSimpleRunner($this);
            $listener->expectOnce('paintCaseStart', array('a_case'));
            $listener->expectOnce('paintCaseEnd', array('a_case'));
            $parser = &new SimpleTestXmlParser($listener);
            $parser->parse("<?xml version=\"1.0\"?>\n");
            $parser->parse("<run>\n");
            $this->assertTrue($parser->parse("<case>\n"));
            $this->assertTrue($parser->parse("<name>a_case</name>\n"));
            $this->assertTrue($parser->parse("</case>\n"));
            $parser->parse("</run>\n");
            $listener->tally();
        }
        function testEmptyMethod() {
            $listener = &new MockSimpleRunner($this);
            $listener->expectOnce('paintCaseStart', array('a_case'));
            $listener->expectOnce('paintCaseEnd', array('a_case'));
            $listener->expectOnce('paintMethodStart', array('a_method'));
            $listener->expectOnce('paintMethodEnd', array('a_method'));
            $parser = &new SimpleTestXmlParser($listener);
            $parser->parse("<?xml version=\"1.0\"?>\n");
            $parser->parse("<run>\n");
            $parser->parse("<case>\n");
            $parser->parse("<name>a_case</name>\n");
            $this->assertTrue($parser->parse("<test>\n"));
            $this->assertTrue($parser->parse("<name>a_method</name>\n"));
            $this->assertTrue($parser->parse("</test>\n"));
            $parser->parse("</case>\n");
            $parser->parse("</run>\n");
            $listener->tally();
        }
        function testNestedGroup() {
            $listener = &new MockSimpleRunner($this);
            $listener->expectArgumentsAt(0, 'paintGroupStart', array('a_group', 7));
            $listener->expectArgumentsAt(1, 'paintGroupStart', array('b_group', 3));
            $listener->expectCallCount('paintGroupStart', 2);
            $listener->expectArgumentsAt(0, 'paintGroupEnd', array('b_group'));
            $listener->expectArgumentsAt(1, 'paintGroupEnd', array('a_group'));
            $listener->expectCallCount('paintGroupEnd', 2);
            $parser = &new SimpleTestXmlParser($listener);
            $parser->parse("<?xml version=\"1.0\"?>\n");
            $parser->parse("<run>\n");
            $this->assertTrue($parser->parse("<group size=\"7\">\n"));
            $this->assertTrue($parser->parse("<name>a_group</name>\n"));
            $this->assertTrue($parser->parse("<group size=\"3\">\n"));
            $this->assertTrue($parser->parse("<name>b_group</name>\n"));
            $this->assertTrue($parser->parse("</group>\n"));
            $this->assertTrue($parser->parse("</group>\n"));
            $parser->parse("</run>\n");
            $listener->tally();
        }
    }
    
    class AnyOldSignal {
        var $stuff = true;
    }
    
    class TestOfXmlResultsParsing extends UnitTestCase {
        function TestOfXmlResultsParsing() {
            $this->UnitTestCase();
        }
        function sendValidStart(&$parser) {
            $parser->parse("<?xml version=\"1.0\"?>\n");
            $parser->parse("<run>\n");
            $parser->parse("<case>\n");
            $parser->parse("<name>a_case</name>\n");
            $parser->parse("<test>\n");
            $parser->parse("<name>a_method</name>\n");
        }
        function sendValidEnd(&$parser) {
            $parser->parse("</test>\n");
            $parser->parse("</case>\n");
            $parser->parse("</run>\n");
        }
        function testPass() {
            $listener = &new MockSimpleRunner($this);
            $listener->expectOnce('paintPass', array('a_message'));
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse("<pass>a_message</pass>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
        function testFail() {
            $listener = &new MockSimpleRunner($this);
            $listener->expectOnce('paintFail', array('a_message'));
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse("<fail>a_message</fail>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
        function testException() {
            $listener = &new MockSimpleRunner($this);
            $listener->expectOnce('paintException', array('a_message'));
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse("<exception>a_message</exception>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
        function testSignal() {
            $signal = new AnyOldSignal();
            $signal->stuff = "Hello";
            $listener = &new MockSimpleRunner($this);
            $listener->expectOnce('paintSignal', array('a_signal', $signal));
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse(
                    "<signal type=\"a_signal\"><![CDATA[" .
                    serialize($signal) . "]]></signal>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
        function testMessage() {
            $listener = &new MockSimpleRunner($this);
            $listener->expectOnce('paintMessage', array('a_message'));
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse("<message>a_message</message>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
        function testFormattedMessage() {
            $listener = &new MockSimpleRunner($this);
            $listener->expectOnce('paintFormattedMessage', array("\na\tmessage\n"));
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse("<formatted><![CDATA[\na\tmessage\n]]></formatted>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
    }
?>