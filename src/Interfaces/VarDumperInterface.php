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

use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarDump\Processors\ArrayProcessor;
use Chevere\VarDump\Processors\BooleanProcessor;
use Chevere\VarDump\Processors\FloatProcessor;
use Chevere\VarDump\Processors\IntegerProcessor;
use Chevere\VarDump\Processors\NullProcessor;
use Chevere\VarDump\Processors\ObjectProcessor;
use Chevere\VarDump\Processors\ResourceProcessor;
use Chevere\VarDump\Processors\StringProcessor;
use Chevere\Writer\Interfaces\WriterInterface;
use Ds\Set;

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

    public const MODIFIERS = '_modifiers';

    public const VARIABLE = '_variable';

    public const EMPHASIS = '_emphasis';

    /**
     * @var array [ProcessorInterface,]
     */
    public const PROCESSORS = [
        TypeInterface::BOOLEAN => BooleanProcessor::class,
        TypeInterface::ARRAY => ArrayProcessor::class,
        TypeInterface::OBJECT => ObjectProcessor::class,
        TypeInterface::INTEGER => IntegerProcessor::class,
        TypeInterface::STRING => StringProcessor::class,
        TypeInterface::FLOAT => FloatProcessor::class,
        TypeInterface::NULL => NullProcessor::class,
        TypeInterface::RESOURCE => ResourceProcessor::class,
    ];

    public function __construct(
        WriterInterface $writer,
        FormatInterface $format,
        VarDumpableInterface $dumpable
    );

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
     * Return an instance with the specified `$known` object IDs.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$known` object IDs.
     */
    public function withKnownObjects(Set $known): self;

    /**
     * Provides access to the known object ids.
     */
    public function known(): Set;

    /**
     * Process the dump.
     */
    public function withProcess(): self;
}
