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

/**
 * Describes the component in charge of format a var dump document.
 */
interface DocumentFormatInterface
{
    /**
     * Provides access to the VarDumpFormatInterface instance.
     */
    public function varDumpFormat(): FormatInterface;

    /**
     * Get a new object implementing the VarDumpFormatInterface.
     */
    public function getVarDumpFormat(): FormatInterface;

    /**
     * Returns the template used for items in the document.
     */
    public function getItemTemplate(): string;

    /**
     * Returns formatted horizontal rule.
     */
    public function getHr(): string;

    /**
     * Returns formatted line break.
     */
    public function getLineBreak(): string;

    /**
     * Returns `$value` formatted as wrapped link.
     */
    public function wrapLink(string $value): string;

    /**
     * Returns `$value` formatted as hidden element.
     */
    public function wrapHidden(string $value): string;

    /**
     * Returns `$value` formatted as section title.
     */
    public function wrapSectionTitle(string $value): string;

    /**
     * Returns `$value` formatted as title.
     */
    public function wrapTitle(string $value): string;
}
