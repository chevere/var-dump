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

namespace Chevere\VarDump\Highlights;

use Chevere\Parameter\Interfaces\TypeInterface;
use Chevere\VarDump\Highlights\Traits\AssertKeyTrait;
use Chevere\VarDump\Interfaces\HighlightInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;

final class HtmlHighlight implements HighlightInterface
{
    use AssertKeyTrait;

    private string $class;

    public function __construct(
        private string $key
    ) {
        $this->assertKey($key);
        $this->class = $this->palette()[$this->key]
            ?? '';
    }

    public function highlight(string $dump): string
    {
        return <<<HTML
        <span class="chv-dump-{$this->class}">{$dump}</span>
        HTML;
    }

    /**
     * @return array<string, string>
     */
    public static function palette(): array
    {
        return [
            TypeInterface::STRING => 'string',
            TypeInterface::FLOAT => 'float',
            TypeInterface::INT => 'int',
            TypeInterface::BOOL => 'bool',
            TypeInterface::NULL => 'null',
            TypeInterface::OBJECT => 'object',
            TypeInterface::ARRAY => 'array',
            TypeInterface::RESOURCE => 'resource',
            VarDumperInterface::FILE => 'file',
            VarDumperInterface::CLASS_REG => 'class',
            VarDumperInterface::OPERATOR => 'operator',
            VarDumperInterface::FUNCTION => 'function',
            VarDumperInterface::VARIABLE => 'variable',
            VarDumperInterface::MODIFIER => 'modifier',
            VarDumperInterface::EMPHASIS => 'emphasis',
        ];
    }
}
