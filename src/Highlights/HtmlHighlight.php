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

    private string $color;

    public function __construct(
        private string $key
    ) {
        $this->assertKey($key);
        $this->color = $this->palette()[$this->key] ?? 'inherit';
    }

    public function getHighlight(string $dump): string
    {
        return '<span style="color:' . $this->color . '">' . $dump . '</span>';
    }

    /**
     * @return array<string, string>
     */
    public static function palette(): array
    {
        return [
            TypeInterface::STRING => '#ff8700',
            TypeInterface::FLOAT => '#ff8700',
            TypeInterface::INT => '#ff8700',
            TypeInterface::BOOL => '#ff8700',
            TypeInterface::NULL => '#ff8700',
            TypeInterface::OBJECT => '#fabb00',
            TypeInterface::ARRAY => '#27ae60',
            TypeInterface::RESOURCE => '#ff5f5f',
            VarDumperInterface::FILE => '#87afff',
            VarDumperInterface::CLASS_REG => '#fabb00',
            VarDumperInterface::OPERATOR => '#6c6c6c',
            VarDumperInterface::FUNCTION => '#00afff',
            VarDumperInterface::VARIABLE => '#00afff',
            VarDumperInterface::MODIFIERS => '#d75fd7',
            VarDumperInterface::EMPHASIS => 'rgb(108 108 108 / 65%);',
        ];
    }
}
