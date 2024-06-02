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

namespace Chevere\Tests;

use Chevere\VarDump\ObjectIds;
use PHPUnit\Framework\TestCase;

final class ObjectIdsTest extends TestCase
{
    public function testToArray(): void
    {
        $objectReferences = new ObjectIds();
        $this->assertSame([], $objectReferences->toArray());
        $objectReferences->push(1);
        $this->assertSame([1], $objectReferences->toArray());
    }

    public function testPushHas(): void
    {
        $objectReferences = new ObjectIds();
        $objectReferences->push(1);
        $this->assertTrue($objectReferences->has(1));
        $objectReferences->push(1);
        $objectReferences->push(2);
        $this->assertTrue($objectReferences->has(2));
        $this->assertSame([1, 2], $objectReferences->toArray());
    }
}
