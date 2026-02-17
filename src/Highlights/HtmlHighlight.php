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
        $this->class = $this->palette()[$this->key] ?? '';
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
            'string' => 'string',
            'float' => 'float',
            'int' => 'int',
            'bool' => 'bool',
            'null' => 'null',
            'object' => 'object',
            'array' => 'array',
            'resource' => 'resource',
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
