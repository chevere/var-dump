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

use Chevere\Tests\Traits\VarDumperTrait;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\VarDump\Processors\StringProcessor;
use PHPUnit\Framework\TestCase;

final class StringProcessorTest extends TestCase
{
    use VarDumperTrait;

    /**
     * @dataProvider provideConstruct
     */
    public function testConstruct(string $var, string $expected = ''): void
    {
        if ($expected === '') {
            $expected = $var;
        }
        $varDumper = $this->getVarDumper($var);
        $this->assertProcessor(StringProcessor::class, $varDumper);
        $processor = new StringProcessor($varDumper);
        $this->assertSame('CP1252', $processor->charset());
        $expectedInfo = 'length=' . mb_strlen($var);
        $this->assertSame($expectedInfo, $processor->info(), "info:{$var}");
        $this->assertSame(
            "string {$expected} ({$expectedInfo})",
            $varDumper->writer()->__toString(),
            "string:{$expected}"
        );
    }

    public function provideConstruct(): array
    {
        return [
            [''],
            ['string'],
            ['cádena'],
            ['another string'],
            ['100'],
            ['false'],
            ['😀'],
            ['€'],
            [chr(128), 'b"€"'],
        ];
    }

    public function testTypeError(): void
    {
        $varDumper = $this->getVarDumper(123);
        $this->expectException(InvalidArgumentException::class);
        new StringProcessor($varDumper);
    }
}
