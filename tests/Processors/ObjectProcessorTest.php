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
use Chevere\VarDump\Interfaces\ProcessorInterface;
use Chevere\VarDump\Processors\ObjectProcessor;
use Ds\Map;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
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
        $className#$id
        public public null
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
        $className#$id
        public public stdClass#$pubId
         public string string string (length=6)
         public array array [] (size=0)
         public int integer 1 (length=1)
         public bool boolean true
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
        $className#$id
        public public null
        private private null
        private protected null
        private circularReference $className#$id (circular reference #$id)
        private deep null
        EOT;
        $this->assertSame(
            $dump,
            $varDumper->writer()->__toString()
        );
    }

    public function testDeep(): void
    {
        $deep = new stdClass();
        for ($i = 0; $i <= ProcessorInterface::MAX_DEPTH; $i++) {
            $deep = new class($deep) {
                public function __construct(public $deep)
                {
                }
            };
            $objectIds[] = strval(spl_object_id($deep));
        }
        $objectIds = array_reverse($objectIds);
        $object = (new DummyClass())->withDeep($deep);
        $className = $object::class;
        $id = strval(spl_object_id($object));
        $varDumper = $this->getVarDumper($object);
        $stringEls = <<<EOT
        $className#$id
        public public null
        private private null
        private protected null
        private circularReference null
        private deep class@anonymous#$objectIds[0]
         public deep class@anonymous#$objectIds[1]
          public deep class@anonymous#$objectIds[2]
           public deep class@anonymous#$objectIds[3]
            public deep class@anonymous#$objectIds[4]
             public deep class@anonymous#$objectIds[5]
              public deep class@anonymous#$objectIds[6]
               public deep class@anonymous#$objectIds[7]
                public deep class@anonymous#$objectIds[8]
                 public deep class@anonymous#$objectIds[9] (max depth reached)
        EOT;
        $this->assertSame(
            $stringEls,
            $varDumper->writer()->__toString()
        );
    }

    public function testDsMap(): void
    {
        $key = 'key';
        $value = 'value';
        $objectChild = new Map(['test']);
        $object = new Map([$key => $value, 'map' => $objectChild]);
        $className = $object::class;
        $id = strval(spl_object_id($object));
        $idChild = strval(spl_object_id($objectChild));
        $objectIds = [];
        $reflection = new ReflectionClass(Map::class);
        foreach ([$object, $objectChild] as $map) {
            $property = $reflection->getProperty('pairs');
            $property->setAccessible(true);
            $pairs = $property->getValue($map);
            foreach ($pairs as $pair) {
                $objectIds[] = strval(spl_object_id($pair));
            }
        }
        $varDumper = $this->getVarDumper($object);
        $stringEls = <<<EOT
        $className#$id
        private pairs array (size=2)
         0 => Ds\Pair#$objectIds[0]
          public key string key (length=3)
          public value string value (length=5)
         1 => Ds\Pair#$objectIds[1]
          public key string map (length=3)
          public value $className#$idChild
           private pairs array (size=1)
            0 => Ds\Pair#$objectIds[2]
             public key integer 0 (length=1)
             public value string test (length=4)
           private capacity integer 8 (length=1)
        private capacity integer 8 (length=1)
        EOT;
        $this->assertSame(
            $stringEls,
            $varDumper->writer()->__toString()
        );
    }
}
