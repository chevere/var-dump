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

namespace Chevere\VarDump\Processors\Traits;

use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Type\Type;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use function Chevere\Message\message;

trait ProcessorTrait
{
    private VarDumperInterface $varDumper;

    private string $info = '';

    abstract public function type(): string;

    public function info(): string
    {
        return $this->info;
    }

    public function typeHighlighted(): string
    {
        return $this->varDumper->format()
            ->getHighlight($this->type(), $this->type());
    }

    public function highlightOperator(string $string): string
    {
        return $this->varDumper->format()
            ->getHighlight(
                VarDumperInterface::OPERATOR,
                $string
            );
    }

    public function highlightParentheses(string $string): string
    {
        return $this->varDumper->format()->getEmphasis("({$string})");
    }

    public function circularReference(): string
    {
        return 'circular reference';
    }

    public function maxDepthReached(): string
    {
        return 'max depth reached';
    }

    private function assertType(): void
    {
        $type = new Type($this->type());
        if (! $type->validate($this->varDumper->dumpable()->var())) {
            throw new InvalidArgumentException(
                message('Instance of %className% expects a type %expected% for the return value of %method%, type %provided% returned')
                    ->withCode('%className%', static::class)
                    ->withCode('%expected%', $this->type())
                    ->withCode('%method%', $this->varDumper::class . '::var()')
                    ->withCode('%provided%', get_debug_type($this->varDumper->dumpable()->var()))
            );
        }
    }
}
