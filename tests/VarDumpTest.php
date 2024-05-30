<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests;

use Chevere\VarDump\Formats\HtmlFormat;
use Chevere\VarDump\Formats\PlainFormat;
use Chevere\VarDump\Interfaces\VarDumpInterface;
use Chevere\VarDump\Outputs\PlainOutput;
use Chevere\VarDump\VarDump;
use Chevere\Writer\StreamWriter;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Chevere\Writer\streamTemp;

final class VarDumpTest extends TestCase
{
    public function testConstruct(): void
    {
        $varDump = $this->getVarDump();
        $this->assertSame(0, $varDump->shift());
        $this->assertSame([], $varDump->variables());
    }

    public function testWithVariables(): void
    {
        $stream = streamTemp('');
        $writer = new StreamWriter($stream);
        $var = new stdClass();
        $varDump = $this->getVarDump();
        $varDumpWithVariables = $varDump->withVariables($var);
        $this->assertNotSame($varDump, $varDumpWithVariables);
        $this->assertEqualsCanonicalizing(
            [$var],
            $varDumpWithVariables->variables()
        );
        $varDumpWithVariables->process($writer);
        $line = strval(__LINE__ - 1);
        $className = $varDump::class;
        $fileLine = __FILE__ . ':' . $line;
        $objectId = spl_object_id($var);
        $expectedString = <<<PLAIN

        {$className}->process()
        ------------------------------------------------------------
        {$fileLine}

        Arg#1 stdClass#{$objectId}
        ------------------------------------------------------------

        PLAIN;
        $this->assertSame($expectedString, $writer->__toString());
    }

    public function testCircularReferenceArguments(): void
    {
        $var = new stdClass();
        $var->circular = $var;
        $var->string = 'test';
        $varDump = $this->getVarDump();
        $with = $varDump->withVariables($var, [$var]);
        $stream = streamTemp('');
        $writer = new StreamWriter($stream);
        $with->process($writer);
        $line = strval(__LINE__ - 1);
        $className = $varDump::class;
        $fileLine = __FILE__ . ':' . $line;
        $objectId = spl_object_id($var);
        $expectedString = <<<PLAIN

        {$className}->process()
        ------------------------------------------------------------
        {$fileLine}

        Arg#1 stdClass#{$objectId}
        public circular stdClass#{$objectId} (circular reference #{$objectId})
        public string string test (length=4)

        Arg#2 array (size=1)
        0 => stdClass#{$objectId}
         public circular stdClass#{$objectId} (circular reference #{$objectId})
         public string string test (length=4)
        ------------------------------------------------------------

        PLAIN;
        $this->assertSame($expectedString, $writer->__toString());
    }

    public function testWithShift(): void
    {
        $stream = streamTemp('');
        $writer = new StreamWriter($stream);
        $varDump = $this->getVarDump();
        $varDumpWithShift = $varDump->withShift(1);
        $this->assertNotSame($varDump, $varDumpWithShift);
        $this->assertSame(1, $varDumpWithShift->shift());
        $varDumpWithShift->process($writer);
    }

    public function testWithArrayNeedsPullUp(): void
    {
        $vars = [
            0 => [],
            1 => [0, 1],
            2 => [
                0 => [1],
                1 => null,
                3 => [[[5]]],
                2 => [2],
            ],
            3 => [
                'key' => 'value',
            ],
            4 => null,
        ];
        $stream = streamTemp('');
        $writer = new StreamWriter($stream);
        $varDump = $this->getVarDump();
        $with = $varDump->withVariables(...$vars);
        $with->process($writer);
        $line = strval(__LINE__ - 1);
        $className = $varDump::class;
        $fileLine = __FILE__ . ':' . $line;
        $expectedString = <<<PLAIN

        {$className}->process()
        ------------------------------------------------------------
        {$fileLine}

        Arg#1 array [] (size=0)

        Arg#2 array (size=2)
        0 => int 0 (length=1)
        1 => int 1 (length=1)

        Arg#3 array (size=4)
        0 => array (size=1)
         0 => int 1 (length=1)
        1 => null
        3 => array (size=1)
         0 => array (size=1)
          0 => array (size=1)
           0 => int 5 (length=1)
        2 => array (size=1)
         0 => int 2 (length=1)

        Arg#4 array (size=1)
        key => string value (length=5)

        Arg#5 null
        ------------------------------------------------------------

        PLAIN;
        $this->assertSame($expectedString, $writer->__toString());
        $stream = streamTemp('');
        $writer = new StreamWriter($stream);
        $varDump = new VarDump(new HtmlFormat(), new PlainOutput());
        $with = $varDump->withVariables(...$vars);
        $with->process($writer);
        $line = strval(__LINE__ - 1);
        $className = $varDump::class;
        $fileLine = __FILE__ . ':' . $line;
        $expectedString = <<<PLAIN

        {$className}->process()
        ------------------------------------------------------------
        <span style="color:#87afff">{$fileLine}</span>

        Arg#1 <span style="color:#27ae60">array</span> [] <em><span style="color:rgb(108 108 108 / 65%);">(size=0)</span></em>

        Arg#2 <span style="color:#27ae60">array</span> <em><span style="color:rgb(108 108 108 / 65%);">(size=2)</span></em><details style="line-height: normal; display: block; margin-top: -1.2em;" open><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary>0 <span style="color:#6c6c6c">=></span> <span style="color:#ff8700">int</span> 0 <em><span style="color:rgb(108 108 108 / 65%);">(length=1)</span></em>
        1 <span style="color:#6c6c6c">=></span> <span style="color:#ff8700">int</span> 1 <em><span style="color:rgb(108 108 108 / 65%);">(length=1)</span></em></details>

        Arg#3 <span style="color:#27ae60">array</span> <em><span style="color:rgb(108 108 108 / 65%);">(size=4)</span></em><details style="line-height: normal; display: block; margin-top: -1.2em;" open><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary>0 <span style="color:#6c6c6c">=></span> <span style="color:#27ae60">array</span> <em><span style="color:rgb(108 108 108 / 65%);">(size=1)</span></em><details style="line-height: normal; display: block; margin-top: -1.2em;"><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary> <span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span> 0 <span style="color:#6c6c6c">=></span> <span style="color:#ff8700">int</span> 1 <em><span style="color:rgb(108 108 108 / 65%);">(length=1)</span></em></details><div style="margin-top: -1.2em; height: 0;"></div>
        1 <span style="color:#6c6c6c">=></span> <span style="color:#ff8700">null</span>
        3 <span style="color:#6c6c6c">=></span> <span style="color:#27ae60">array</span> <em><span style="color:rgb(108 108 108 / 65%);">(size=1)</span></em><details style="line-height: normal; display: block; margin-top: -1.2em;"><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary> <span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span> 0 <span style="color:#6c6c6c">=></span> <span style="color:#27ae60">array</span> <em><span style="color:rgb(108 108 108 / 65%);">(size=1)</span></em><details style="line-height: normal; display: block; margin-top: -1.2em;"><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary> <span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span>  <span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span> 0 <span style="color:#6c6c6c">=></span> <span style="color:#27ae60">array</span> <em><span style="color:rgb(108 108 108 / 65%);">(size=1)</span></em><details style="line-height: normal; display: block; margin-top: -1.2em;"><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary> <span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span>  <span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span>  <span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span> 0 <span style="color:#6c6c6c">=></span> <span style="color:#ff8700">int</span> 5 <em><span style="color:rgb(108 108 108 / 65%);">(length=1)</span></em></details></details></details><div style="margin-top: -1.2em; height: 0;"></div>
        2 <span style="color:#6c6c6c">=></span> <span style="color:#27ae60">array</span> <em><span style="color:rgb(108 108 108 / 65%);">(size=1)</span></em><details style="line-height: normal; display: block; margin-top: -1.2em;"><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary> <span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span> 0 <span style="color:#6c6c6c">=></span> <span style="color:#ff8700">int</span> 2 <em><span style="color:rgb(108 108 108 / 65%);">(length=1)</span></em></details></details>

        Arg#4 <span style="color:#27ae60">array</span> <em><span style="color:rgb(108 108 108 / 65%);">(size=1)</span></em><details style="line-height: normal; display: block; margin-top: -1.2em;" open><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary>key <span style="color:#6c6c6c">=></span> <span style="color:#ff8700">string</span> value <em><span style="color:rgb(108 108 108 / 65%);">(length=5)</span></em></details>

        Arg#5 <span style="color:#ff8700">null</span>
        ------------------------------------------------------------

        PLAIN;
        $this->assertSame($expectedString, $writer->__toString());
    }

    public function testWithObjectNeedsPullUp(): void
    {
        $one = new stdClass();
        $two = new stdClass();
        $two->zero = 0;
        $two->one = 1;
        $nested = new stdClass();
        $three = new stdClass();
        $three->nested = $nested;
        $three->two = $two;
        $three->three = false;
        $vars = [
            0 => $one,
            1 => $two,
            2 => $three,
        ];
        $oneId = spl_object_id($one);
        $twoId = spl_object_id($two);
        $threeId = spl_object_id($three);
        $nestedId = spl_object_id($nested);
        $stream = streamTemp('');
        $writer = new StreamWriter($stream);
        $varDump = $this->getVarDump();
        $with = $varDump->withVariables(...$vars);
        $with->process($writer);
        $line = strval(__LINE__ - 1);
        $className = $varDump::class;
        $fileLine = __FILE__ . ':' . $line;
        $expectedString = <<<PLAIN

        {$className}->process()
        ------------------------------------------------------------
        {$fileLine}

        Arg#1 stdClass#{$oneId}

        Arg#2 stdClass#{$twoId}
        public zero int 0 (length=1)
        public one int 1 (length=1)

        Arg#3 stdClass#{$threeId}
        public nested stdClass#{$nestedId}
        public two stdClass#{$twoId}
         public zero int 0 (length=1)
         public one int 1 (length=1)
        public three bool false
        ------------------------------------------------------------

        PLAIN;
        $this->assertSame($expectedString, $writer->__toString());
        $stream = streamTemp('');
        $writer = new StreamWriter($stream);
        $varDump = new VarDump(new HtmlFormat(), new PlainOutput());
        $with = $varDump->withVariables(...$vars);
        $with->process($writer);
        $line = strval(__LINE__ - 1);
        $className = $varDump::class;
        $fileLine = __FILE__ . ':' . $line;
        $expectedString = <<<PLAIN

        {$className}->process()
        ------------------------------------------------------------
        <span style="color:#87afff">{$fileLine}</span>

        Arg#1 <span style="color:#fabb00">stdClass</span><span style="color:#6c6c6c">#{$oneId}</span>

        Arg#2 <span style="color:#fabb00">stdClass</span><span style="color:#6c6c6c">#{$twoId}</span><details style="line-height: normal; display: block; margin-top: -1.2em;" open><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary><span style="color:#d75fd7">public</span> <span style="color:#00afff">zero</span> <span style="color:#ff8700">int</span> 0 <em><span style="color:rgb(108 108 108 / 65%);">(length=1)</span></em>
        <span style="color:#d75fd7">public</span> <span style="color:#00afff">one</span> <span style="color:#ff8700">int</span> 1 <em><span style="color:rgb(108 108 108 / 65%);">(length=1)</span></em></details>

        Arg#3 <span style="color:#fabb00">stdClass</span><span style="color:#6c6c6c">#{$threeId}</span><details style="line-height: normal; display: block; margin-top: -1.2em;" open><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary><span style="color:#d75fd7">public</span> <span style="color:#00afff">nested</span> <span style="color:#fabb00">stdClass</span><span style="color:#6c6c6c">#{$nestedId}</span>
        <span style="color:#d75fd7">public</span> <span style="color:#00afff">two</span> <span style="color:#fabb00">stdClass</span><span style="color:#6c6c6c">#{$twoId}</span><details style="line-height: normal; display: block; margin-top: -1.2em;"><summary style="line-height: 1em; height: 1.2em; left: -0.9em; position: relative;"></summary> <span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span> <span style="color:#d75fd7">public</span> <span style="color:#00afff">zero</span> <span style="color:#ff8700">int</span> 0 <em><span style="color:rgb(108 108 108 / 65%);">(length=1)</span></em>
         <span style="border-left: 1px solid rgba(108 108 108 / 35%);"></span> <span style="color:#d75fd7">public</span> <span style="color:#00afff">one</span> <span style="color:#ff8700">int</span> 1 <em><span style="color:rgb(108 108 108 / 65%);">(length=1)</span></em></details><div style="margin-top: -1.2em; height: 0;"></div>
        <span style="color:#d75fd7">public</span> <span style="color:#00afff">three</span> <span style="color:#ff8700">bool</span> false</details>
        ------------------------------------------------------------

        PLAIN;
        $this->assertSame($expectedString, $writer->__toString());
    }

    private function getVarDump(): VarDumpInterface
    {
        return new VarDump(new PlainFormat(), new PlainOutput());
    }
}
