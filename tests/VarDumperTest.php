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

use Chevere\DataStructure\Interfaces\VectorInterface;
use Chevere\DataStructure\Vector;
use Chevere\VarDump\Formats\PlainFormat;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\VarDumpable;
use Chevere\VarDump\VarDumper;
use function Chevere\Writer\streamTemp;
use Chevere\Writer\StreamWriter;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarDumperTest extends TestCase
{
    public function testConstruct(): void
    {
        $variable = ['foo', new stdClass()];
        $defaultIndent = 0;
        $defaultDepth = 0;
        $defaultIndentSting = '';
        $writer = new StreamWriter(streamTemp(''));
        $format = new PlainFormat();
        $dumpable = new VarDumpable($variable);
        $varDumper = new VarDumper(
            writer: $writer,
            format: $format,
            dumpable: $dumpable
        );
        $this->assertSame($writer, $varDumper->writer());
        $this->assertSame($format, $varDumper->format());
        $this->assertSame($dumpable, $varDumper->dumpable());
        $this->assertSame($defaultIndent, $varDumper->indent());
        $this->assertSame($defaultDepth, $varDumper->depth());
        $this->assertSame($defaultIndentSting, $varDumper->indentString());
        $this->assertCount(0, $varDumper->knownObjectsId());
        for ($integer = 1; $integer <= 5; $integer++) {
            $this->hookTestWithIndent($varDumper, $integer);
            $this->hookTestWithDepth($varDumper, $integer);
            $varDumperWithProcess = $this->hookTestWithProcess(
                $varDumperWithProcess ?? $varDumper,
                $integer
            );
        }
        $object1 = new stdClass();
        $object2 = new stdClass();
        $vector = new Vector(spl_object_id($object1), spl_object_id($object2));
        $this->hookTestWithKnownObjectIds(
            $varDumper,
            $vector
        );
    }

    public function hookTestWithIndent(VarDumperInterface $varDumper, int $indent): void
    {
        $varDumperWithIndent = $varDumper->withIndent($indent);
        $this->assertNotSame($varDumper, $varDumperWithIndent);
        $this->assertSame($indent, $varDumperWithIndent->indent());
        $this->assertSame(
            str_repeat(' ', $indent),
            $varDumperWithIndent->indentString()
        );
    }

    public function hookTestWithDepth(
        VarDumperInterface $varDumper,
        int $depth
    ): void {
        $varDumperWithDepth = $varDumper->withDepth($depth);
        $this->assertNotSame($varDumper, $varDumperWithDepth);
        $this->assertSame($depth, $varDumperWithDepth->depth());
    }

    public function hookTestWithKnownObjectIds(
        VarDumperInterface $varDumper,
        VectorInterface $known
    ): void {
        $withKnownObjectIds = $varDumper->withKnownObjectsId($known);
        $this->assertNotSame($varDumper, $withKnownObjectIds);
        $this->assertSame($known, $withKnownObjectIds->knownObjectsId());
    }

    public function hookTestWithProcess(
        VarDumperInterface $varDumper,
        int $indent
    ): VarDumperInterface {
        $varDumperWithProcess = $varDumper->withProcess();
        $this->assertNotSame($varDumper, $varDumperWithProcess);
        $this->assertSame($indent, $varDumperWithProcess->indent());

        return $varDumperWithProcess;
    }
}
