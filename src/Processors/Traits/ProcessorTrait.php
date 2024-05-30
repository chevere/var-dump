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

use Chevere\Parameter\Type;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use InvalidArgumentException;
use function Chevere\Message\message;
use function Chevere\Parameter\getType;

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

    private function assertType(): void
    {
        $type = new Type($this->type());
        if ($type->validate($this->varDumper->dumpable()->var())) {
            return;
        }
        $provided = getType($this->varDumper->dumpable()->var());
        $method = $this->varDumper::class . '::var()';

        throw new InvalidArgumentException(
            (string) message(
                <<<PLAIN
                Instance of `%className%` expects type `%expected%` for the return value of `%method%`, type `%provided%` returned
                PLAIN,
                className: static::class,
                expected: $this->type(),
                method: $method,
                provided: $provided,
            )
        );
    }
}
