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

use Chevere\VarDump\Interfaces\VarDumperInterface;

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
            ->highlight($this->type(), $this->type());
    }

    public function highlightOperator(string $string): string
    {
        return $this->varDumper->format()
            ->highlight(
                VarDumperInterface::OPERATOR,
                $string
            );
    }

    public function highlightParentheses(string $string): string
    {
        return $this->varDumper->format()->emphasis("({$string})");
    }

    public function circularReference(): string
    {
        return 'circular reference';
    }

    public function maxDepthReached(): string
    {
        return 'max depth reached';
    }
}
