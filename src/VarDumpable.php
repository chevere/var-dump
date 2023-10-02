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

namespace Chevere\VarDump;

use Chevere\Throwable\Exceptions\LogicException;
use Chevere\VarDump\Interfaces\ProcessorInterface;
use Chevere\VarDump\Interfaces\VarDumpableInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use function Chevere\Message\message;
use function Chevere\Type\getType;

final class VarDumpable implements VarDumpableInterface
{
    private string $type;

    private string $processorName;

    public function __construct(
        private mixed $var
    ) {
        $this->type = getType($this->var);
        $this->assertSetProcessorName();
    }

    public function var(): mixed
    {
        return $this->var;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function processorName(): string
    {
        return $this->processorName;
    }

    /**
     * @codeCoverageIgnore
     * @infection-ignore-all
     */
    private function assertSetProcessorName(): void
    {
        $processorName = VarDumperInterface::PROCESSORS[$this->type] ?? null;
        if (! isset($processorName)) {
            throw new LogicException(
                message('No processor for variable of type %type%')
                    ->withCode('%type%', $this->type)
            );
        }
        if (! is_subclass_of($processorName, ProcessorInterface::class, true)) {
            throw new LogicException(
                message('Processor %processorName% must implement the %interfaceName% interface')
                    ->withCode('%processorName%', $processorName)
                    ->withCode('%interfaceName%', ProcessorInterface::class)
            );
        }
        $this->processorName = $processorName;
    }
}
