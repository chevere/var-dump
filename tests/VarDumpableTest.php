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

use Chevere\Parameter\Interfaces\TypeInterface;
use Chevere\VarDump\Processors\ArrayProcessor;
use Chevere\VarDump\Processors\BoolProcessor;
use Chevere\VarDump\Processors\FloatProcessor;
use Chevere\VarDump\Processors\IntProcessor;
use Chevere\VarDump\Processors\NullProcessor;
use Chevere\VarDump\Processors\ObjectProcessor;
use Chevere\VarDump\Processors\ResourceProcessor;
use Chevere\VarDump\Processors\StringProcessor;
use Chevere\VarDump\VarDumpable;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarDumpableTest extends TestCase
{
    public function testConstruct(): void
    {
        $variables = [
            TypeInterface::ARRAY => [
                [], ArrayProcessor::class,
            ],
            TypeInterface::BOOL => [
                true, BoolProcessor::class,
            ],
            TypeInterface::FLOAT => [
                1.1, FloatProcessor::class,
            ],
            TypeInterface::INT => [
                1, IntProcessor::class,
            ],
            TypeInterface::NULL => [
                null, NullProcessor::class,
            ],
            TypeInterface::OBJECT => [
                new stdClass(), ObjectProcessor::class,
            ],
            TypeInterface::RESOURCE => [
                fopen(__FILE__, 'r'),
                ResourceProcessor::class,
            ],
            TypeInterface::STRING => [
                '',
                StringProcessor::class,
            ],
        ];
        foreach ($variables as $type => $var) {
            $variableDump = new VarDumpable($var[0]);
            $this->assertSame($var[0], $variableDump->var());
            $this->assertSame($type, $variableDump->type());
        }
    }
}
