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

use Chevere\Tests\_resources\DummyClass;
use Chevere\Tests\Traits\VarDumperTrait;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\VarDump\Processors\ObjectProcessor;
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
        public public null
        public readonly readonly null
        private private null
        private protected null
        private circularReference null
        private deep null
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
        public public stdClass#{$pubId}
         public string string string (length=6)
         public array array [] (size=0)
         public int int 1 (length=1)
         public bool bool true
        public readonly readonly null
        private private null
        private protected null
        private circularReference null
        private deep null
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
        public public null
        public readonly readonly null
        private private null
        private protected null
        private circularReference {$className}#{$id} (circular reference #{$id})
        private deep null
        EOT;
        $this->assertSame(
            $dump,
            $varDumper->writer()->__toString()
        );
    }

    // public function testDeep(): void
    // {
    //     $deep = new stdClass();
    //     for ($i = 0; $i <= ObjectProcessor::MAX_DEPTH; $i++) {
    //         $deep = new class($deep) {
    //             public function __construct(
    //                 public $deep
    //             ) {
    //             }
    //         };
    //         $objectIds[] = strval(spl_object_id($deep));
    //     }
    //     $objectIds = array_reverse($objectIds);
    //     $object = (new DummyClass())->withDeep($deep);
    //     $className = $object::class;
    //     $id = strval(spl_object_id($object));
    //     $varDumper = $this->getVarDumper($object);
    //     $stringEls = <<<EOT
    //     {$className}#{$id}
    //     public public null
    //     private private null
    //     private protected null
    //     private circularReference null
    //     private deep class@anonymous#{$objectIds[0]}
    //      public deep class@anonymous#{$objectIds[1]}
    //       public deep class@anonymous#{$objectIds[2]}
    //        public deep class@anonymous#{$objectIds[3]}
    //         public deep class@anonymous#{$objectIds[4]}
    //          public deep class@anonymous#{$objectIds[5]}
    //           public deep class@anonymous#{$objectIds[6]}
    //            public deep class@anonymous#{$objectIds[7]}
    //             public deep class@anonymous#{$objectIds[8]}
    //              public deep class@anonymous#{$objectIds[9]} (max depth reached)
    //     EOT;
    //     $this->assertSame(
    //         $stringEls,
    //         $varDumper->writer()->__toString()
    //     );
    // }
}
