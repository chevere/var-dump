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

namespace Chevere\Tests\Processors;

use Chevere\Tests\src\DummyClass;
use Chevere\Tests\Traits\VarDumperTrait;
use Chevere\VarDump\Processors\ObjectProcessor;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ObjectProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ObjectProcessor($this->getVarDumper(null));
    }

    public function testEmptyObject(): void
    {
        $object = new stdClass();
        $id = strval(spl_object_id($object));
        $varDumper = $this->getVarDumper($object);
        $this->assertProcessor(ObjectProcessor::class, $varDumper);
        $processor = new ObjectProcessor($varDumper);
        $this->assertSame(1, $processor->depth());
        $this->assertSame(stdClass::class . '#' . $id, $processor->info());
        $this->assertSame(
            stdClass::class . '#' . $id,
            $varDumper->writer()->__toString()
        );
    }

    public function testUnsetObject(): void
    {
        $object = new DummyClass();
        $className = $object::class;
        $id = strval(spl_object_id($object));
        $varDumper = $this->getVarDumper($object);
        $processor = new ObjectProcessor($varDumper);
        $this->assertSame(DummyClass::class . '#' . $id, $processor->info());
        $dump = <<<EOT
        {$className}#{$id}
        public code int 101 (length=3)
        public public uninitialized
        public readonly readonly uninitialized
        private private uninitialized
        private protected uninitialized
        private circularReference uninitialized
        private deep uninitialized
        EOT;
        $this->assertSame(
            $dump,
            $varDumper->writer()->__toString()
        );
    }

    public function testObjectProperty(): void
    {
        $object = (new DummyClass())->withPublic();
        $className = $object::class;
        $id = strval(spl_object_id($object));
        $pubId = strval(spl_object_id($object->public));
        $varDumper = $this->getVarDumper($object);
        $dump = <<<EOT
        {$className}#{$id}
        public code int 101 (length=3)
        public public stdClass#{$pubId}
         public string string string (length=6)
         public array array [] (size=0)
         public int int 1 (length=1)
         public bool bool true
        public readonly readonly uninitialized
        private private uninitialized
        private protected uninitialized
        private circularReference uninitialized
        private deep uninitialized
        EOT;
        $this->assertSame(
            $dump,
            $varDumper->writer()->__toString()
        );
    }

    public function testAnonClass(): void
    {
        $object = new class() {
        };
        $id = strval(spl_object_id($object));
        $varDumper = $this->getVarDumper($object);
        $this->assertSame(
            'class@anonymous#' . $id,
            $varDumper->writer()->__toString()
        );
    }

    public function testCircularReference(): void
    {
        $object = (new DummyClass())->withCircularReference();
        $className = $object::class;
        $id = strval(spl_object_id($object));
        $varDumper = $this->getVarDumper($object);
        $dump = <<<EOT
        {$className}#{$id}
        public code int 101 (length=3)
        public public uninitialized
        public readonly readonly uninitialized
        private private uninitialized
        private protected uninitialized
        private circularReference {$className}#{$id} (circular reference #{$id})
        private deep uninitialized
        EOT;
        $this->assertSame(
            $dump,
            $varDumper->writer()->__toString()
        );
    }

    public function testDeep(): void
    {
        $deep = new stdClass();
        for ($i = 0; $i <= ObjectProcessor::MAX_DEPTH; $i++) {
            $deep = new class($deep) {
                public function __construct(
                    public $deep
                ) {
                }
            };
            $objectIds[] = strval(spl_object_id($deep));
        }
        $objectIds = array_reverse($objectIds);
        $lastId = $objectIds[99];
        $object = (new DummyClass())->withDeep($deep);
        $className = $object::class;
        $id = strval(spl_object_id($object));
        $varDumper = $this->getVarDumper($object);
        $stringEls = <<<EOT
        {$className}#{$id}
        public code int 101 (length=3)
        public public uninitialized
        public readonly readonly uninitialized
        private private uninitialized
        private protected uninitialized
        private circularReference uninitialized
        private deep class@anonymous#{$objectIds[0]}
        EOT;
        $toString = $varDumper->writer()->__toString();
        $this->assertStringStartsWith($stringEls, $toString);
        $stringEls = <<<EOT
        public deep class@anonymous#{$lastId} (max depth reached)
        EOT;
        $this->assertStringEndsWith($stringEls, $toString);
    }
}
