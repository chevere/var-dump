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
use Chevere\VarDump\Processors\StringProcessor;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class StringProcessorTest extends TestCase
{
    use VarDumperTrait;

    public function dataProviderConstruct(): array
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

    /**
     * @dataProvider dataProviderConstruct
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

    public function testDefaultCharset(): void
    {
        $varDumper = $this->getVarDumper('string');
        $defaultCharset = ini_get('default_charset');
        ini_set('default_charset', '8bit');
        $processor = new StringProcessor($varDumper);
        $this->assertSame('8BIT', $processor->charset());
        ini_set('default_charset', $defaultCharset);
    }

    public function testTypeError(): void
    {
        $varDumper = $this->getVarDumper(123);
        $this->expectException(InvalidArgumentException::class);
        new StringProcessor($varDumper);
    }
}
