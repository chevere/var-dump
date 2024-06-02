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

namespace Chevere\VarDump\Interfaces;

use Chevere\Parameter\Interfaces\TypeInterface;
use Chevere\VarDump\Processors\ArrayProcessor;
use Chevere\VarDump\Processors\BoolProcessor;
use Chevere\VarDump\Processors\FloatProcessor;
use Chevere\VarDump\Processors\IntProcessor;
use Chevere\VarDump\Processors\NullProcessor;
use Chevere\VarDump\Processors\ObjectProcessor;
use Chevere\VarDump\Processors\ResourceProcessor;
use Chevere\VarDump\Processors\StringProcessor;
use Chevere\Writer\Interfaces\WriterInterface;

/**
 * Describes the component in charge of handling variable dumping process.
 */
interface VarDumperInterface
{
    public const FILE = '_file';

    public const CLASS_REG = '_class';

    public const CLASS_ANON = 'class@anonymous';

    public const OPERATOR = '_operator';

    public const FUNCTION = '_function';

    public const MODIFIER = '_modifier';

    public const VARIABLE = '_variable';

    public const EMPHASIS = '_emphasis';

    /**
     * @var array<string, string>
     */
    public const PROCESSORS = [
        TypeInterface::BOOL => BoolProcessor::class,
        TypeInterface::ARRAY => ArrayProcessor::class,
        TypeInterface::OBJECT => ObjectProcessor::class,
        TypeInterface::INT => IntProcessor::class,
        TypeInterface::STRING => StringProcessor::class,
        TypeInterface::FLOAT => FloatProcessor::class,
        TypeInterface::NULL => NullProcessor::class,
        TypeInterface::RESOURCE => ResourceProcessor::class,
    ];

    /**
     * Provides access to the `$writer` instance.
     */
    public function writer(): WriterInterface;

    /**
     * Provides access to the `$format` instance.
     */
    public function format(): FormatInterface;

    /**
     * Provides access to the `$dumpable` instance.
     */
    public function dumpable(): VarDumpableInterface;

    /**
     * Return an instance with the specified `$indent`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$indent`.
     */
    public function withIndent(int $indent): self;

    /**
     * Provides access to the instance indent value.
     */
    public function indent(): int;

    /**
     * Provides access to the instance indent string.
     */
    public function indentString(): string;

    /**
     * Return an instance with the specified `$depth`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$depth`.
     */
    public function withDepth(int $depth): self;

    /**
     * Provides access to the instance `$depth`.
     */
    public function depth(): int;

    /**
     * Process the dump.
     */
    public function withProcess(): self;

    public function objectReferences(): ObjectIdsInterface;
}
