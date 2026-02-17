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
            'array' => [
                [], ArrayProcessor::class,
            ],
            'bool' => [
                true, BoolProcessor::class,
            ],
            'float' => [
                1.1, FloatProcessor::class,
            ],
            'int' => [
                1, IntProcessor::class,
            ],
            'null' => [
                null, NullProcessor::class,
            ],
            'object' => [
                new stdClass(), ObjectProcessor::class,
            ],
            'resource' => [
                fopen(__FILE__, 'r'),
                ResourceProcessor::class,
            ],
            'string' => [
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
