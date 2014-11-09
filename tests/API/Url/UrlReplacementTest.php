<?php

use GroupByInc\API\Url\UrlReplacement;
use GroupByInc\API\Url\OperationType;
use GroupByInc\API\Url\ParserException;
use GroupByInc\API\Util\StringBuilder;

class UrlReplacementTest extends PHPUnit_Framework_TestCase
{

    public function testToString()
    {
        $replacement = new UrlReplacement(2, "a", OperationType::Swap);
        $this->assertEquals("2-a", $replacement);
        $replacement = new UrlReplacement(20, "%", OperationType::Insert);
        $this->assertEquals("i20-%", $replacement);
    }

    public function testFromString()
    {
        UrlReplacement::fromString("2-a");
    }

    public function testFromStringInvalidString()
    {
        try {
            UrlReplacement::fromString("a2-a");
            $this->fail("Exception not thrown");
        } catch (ParserException $e) {
            // expected
        }
    }

    public function testToStringAndFromStringWithInsert()
    {
        $this->assertToAndFromString("i2-/");
    }

    public function testToStringAndFromStringWithSwap()
    {
        $this->assertToAndFromString("35-9");
    }

    public function testToStringAndFromStringWithDash()
    {
        $this->assertToAndFromString("35--");
    }

    public function testParseQueryString()
    {
        $this->assertParseQuery("2-a", "3-b", "4-c");
    }

    public function testParseQueryStringWithInserts()
    {
        $this->assertParseQuery("2-a", "i3-b", "4-c");
    }

    public function testParseQueryStringWithDash()
    {
        $this->assertParseQuery("2--", "i3-b", "4-c");
    }

    public function testParseQueryStringWithDash2()
    {
        $this->assertParseQuery("2--", "i3-b", "4--");
    }

    public function testParseQueryStringWithDashMismatch()
    {
        try {
            UrlReplacement::parseQueryString("2-a-i3-b--4-c");
            $this->fail("Exception not thrown");
        } catch (ParserException $e) {
            // expected
        }
    }

    public function testSimpleApplyReplace()
    {
        $this->assertApply("avc123", "abc123", new UrlReplacement(1, "b", OperationType::Swap));
    }

    public function testSimpleApplyReplaceAtStart()
    {
        $this->assertApply("zbc123", "abc123", new UrlReplacement(0, "a", OperationType::Swap));
    }

    public function testSimpleApplyReplaceAtStartBadIndex()
    {
        $this->assertBadApply("zbc123", new UrlReplacement(-1, "a", OperationType::Swap));
    }

    public function testSimpleApplyReplaceAtEnd()
    {
        $this->assertApply("abc124", "abc123", new UrlReplacement(5, "3", OperationType::Swap));
    }

    public function testSimpleApplyReplaceAtEndBadIndex()
    {
        $this->assertBadApply("abc124", new UrlReplacement(6, "3", OperationType::Swap));
    }

    public function testSimpleApplyInsertAtEnd()
    {
        $this->assertApply("abc12", "abc123", new UrlReplacement(5, "3", OperationType::Insert));
    }

    public function testSimpleApplyInsertAtStart()
    {
        $this->assertApply("bc123", "abc123", new UrlReplacement(0, "a", OperationType::Insert));
    }

    public function testSimpleApplyInsertAtStartBadIndex()
    {
        $this->assertBadApply("bc123", new UrlReplacement(-1, "a", OperationType::Insert));
    }

    public function testSimpleApplyInsertAtEndBadIndex()
    {
        $this->assertBadApply("abc12", new UrlReplacement(6, "3", OperationType::Insert));
    }

    private function assertBadApply($input, UrlReplacement $replacement)
    {
        $builder = new StringBuilder($input);
        $replacement->apply($builder, 0);
        $this->assertEquals($input, $builder);
    }

    private function assertApply($input, $expected, UrlReplacement $replacement)
    {
        $builder = new StringBuilder($input);
        $replacement->apply($builder, 0);
        $this->assertEquals($expected, $builder);
    }

    private function assertParseQuery()
    {
        $builder = new StringBuilder();
        $args = func_get_args();
        /** @var string $replacement */
        foreach ($args as $j => $replacement) {
            if ($builder->length() != 0) {
                $builder->append("-");
            }
            $builder->append($replacement);
        }

        $replacements = UrlReplacement::parseQueryString($builder->__toString());

        $this->assertEquals(strlen($replacement), count($replacements));

        for ($i = 0; $i < count($replacements); $i++) {
            $this->assertEquals($args[strlen($replacement) - ($i + 1)], $replacements[$i]->__toString());
        }
    }

    private function assertToAndFromString($replacementString)
    {
        $replacement1 = UrlReplacement::fromString($replacementString);
        $urlReplacementString = (string)$replacement1;
        $replacement2 = UrlReplacement::fromString($urlReplacementString);
        $this->assertEquals($replacement1, $replacement2);
        $this->assertEquals((string)$replacement2, $replacementString);
    }
}
