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

        1: stdClass#{$objectId}
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

        1: stdClass#{$objectId}
        public circular stdClass#{$objectId} (circular reference #{$objectId})
        public string string test (length=4)

        2: array (size=1)
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

        1: array [] (size=0)

        2: array (size=2)
        0 => int 0 (length=1)
        1 => int 1 (length=1)

        3: array (size=4)
        0 => array (size=1)
         0 => int 1 (length=1)
        1 => null
        3 => array (size=1)
         0 => array (size=1)
          0 => array (size=1)
           0 => int 5 (length=1)
        2 => array (size=1)
         0 => int 2 (length=1)

        4: array (size=1)
        key => string value (length=5)

        5: null
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
        <span class="chv-dump-file">{$fileLine}</span>

        1: <span class="chv-dump-array">array</span> [] <em><span class="chv-dump-emphasis">(size=0)</span></em>

        2: <span class="chv-dump-array">array</span> <em><span class="chv-dump-emphasis">(size=2)</span></em><details class="chv-dump-details" open><summary class="chv-dump-summary"></summary>0 <span class="chv-dump-operator">=></span> <span class="chv-dump-int">int</span> 0 <em><span class="chv-dump-emphasis">(length=1)</span></em>
        1 <span class="chv-dump-operator">=></span> <span class="chv-dump-int">int</span> 1 <em><span class="chv-dump-emphasis">(length=1)</span></em></details>

        3: <span class="chv-dump-array">array</span> <em><span class="chv-dump-emphasis">(size=4)</span></em><details class="chv-dump-details" open><summary class="chv-dump-summary"></summary>0 <span class="chv-dump-operator">=></span> <span class="chv-dump-array">array</span> <em><span class="chv-dump-emphasis">(size=1)</span></em><details class="chv-dump-details"><summary class="chv-dump-summary"></summary> <span class="chv-dump-inline"></span> 0 <span class="chv-dump-operator">=></span> <span class="chv-dump-int">int</span> 1 <em><span class="chv-dump-emphasis">(length=1)</span></em></details>
        1 <span class="chv-dump-operator">=></span> <span class="chv-dump-null">null</span>
        3 <span class="chv-dump-operator">=></span> <span class="chv-dump-array">array</span> <em><span class="chv-dump-emphasis">(size=1)</span></em><details class="chv-dump-details"><summary class="chv-dump-summary"></summary> <span class="chv-dump-inline"></span> 0 <span class="chv-dump-operator">=></span> <span class="chv-dump-array">array</span> <em><span class="chv-dump-emphasis">(size=1)</span></em><details class="chv-dump-details"><summary class="chv-dump-summary"></summary> <span class="chv-dump-inline"></span>  <span class="chv-dump-inline"></span> 0 <span class="chv-dump-operator">=></span> <span class="chv-dump-array">array</span> <em><span class="chv-dump-emphasis">(size=1)</span></em><details class="chv-dump-details"><summary class="chv-dump-summary"></summary> <span class="chv-dump-inline"></span>  <span class="chv-dump-inline"></span>  <span class="chv-dump-inline"></span> 0 <span class="chv-dump-operator">=></span> <span class="chv-dump-int">int</span> 5 <em><span class="chv-dump-emphasis">(length=1)</span></em></details></details></details>
        2 <span class="chv-dump-operator">=></span> <span class="chv-dump-array">array</span> <em><span class="chv-dump-emphasis">(size=1)</span></em><details class="chv-dump-details"><summary class="chv-dump-summary"></summary> <span class="chv-dump-inline"></span> 0 <span class="chv-dump-operator">=></span> <span class="chv-dump-int">int</span> 2 <em><span class="chv-dump-emphasis">(length=1)</span></em></details></details>

        4: <span class="chv-dump-array">array</span> <em><span class="chv-dump-emphasis">(size=1)</span></em><details class="chv-dump-details" open><summary class="chv-dump-summary"></summary>key <span class="chv-dump-operator">=></span> <span class="chv-dump-string">string</span> value <em><span class="chv-dump-emphasis">(length=5)</span></em></details>

        5: <span class="chv-dump-null">null</span>
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

        1: stdClass#{$oneId}

        2: stdClass#{$twoId}
        public zero int 0 (length=1)
        public one int 1 (length=1)

        3: stdClass#{$threeId}
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
        <span class="chv-dump-file">{$fileLine}</span>

        1: <span class="chv-dump-class">stdClass</span><span class="chv-dump-operator">#{$oneId}</span>

        2: <span class="chv-dump-class">stdClass</span><span class="chv-dump-operator">#{$twoId}</span><details class="chv-dump-details" open><summary class="chv-dump-summary"></summary><span class="chv-dump-modifier">public</span> <span class="chv-dump-variable">zero</span> <span class="chv-dump-int">int</span> 0 <em><span class="chv-dump-emphasis">(length=1)</span></em>
        <span class="chv-dump-modifier">public</span> <span class="chv-dump-variable">one</span> <span class="chv-dump-int">int</span> 1 <em><span class="chv-dump-emphasis">(length=1)</span></em></details>

        3: <span class="chv-dump-class">stdClass</span><span class="chv-dump-operator">#{$threeId}</span><details class="chv-dump-details" open><summary class="chv-dump-summary"></summary><span class="chv-dump-modifier">public</span> <span class="chv-dump-variable">nested</span> <span class="chv-dump-class">stdClass</span><span class="chv-dump-operator">#{$nestedId}</span>
        <span class="chv-dump-modifier">public</span> <span class="chv-dump-variable">two</span> <span class="chv-dump-class">stdClass</span><span class="chv-dump-operator">#{$twoId}</span><details class="chv-dump-details"><summary class="chv-dump-summary"></summary> <span class="chv-dump-inline"></span> <span class="chv-dump-modifier">public</span> <span class="chv-dump-variable">zero</span> <span class="chv-dump-int">int</span> 0 <em><span class="chv-dump-emphasis">(length=1)</span></em>
         <span class="chv-dump-inline"></span> <span class="chv-dump-modifier">public</span> <span class="chv-dump-variable">one</span> <span class="chv-dump-int">int</span> 1 <em><span class="chv-dump-emphasis">(length=1)</span></em></details>
        <span class="chv-dump-modifier">public</span> <span class="chv-dump-variable">three</span> <span class="chv-dump-bool">bool</span> false</details>
        ------------------------------------------------------------

        PLAIN;
        $this->assertSame($expectedString, $writer->__toString());
    }

    private function getVarDump(): VarDumpInterface
    {
        return new VarDump(new PlainFormat(), new PlainOutput());
    }
}
