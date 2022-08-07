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

namespace Chevere\VarDump\Processors;

use Chevere\String\ValidateString;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\ProcessorInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Processors\Traits\HandleDepthTrait;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;
use Ds\Set;
use Reflection;
use ReflectionObject;
use Throwable;

final class ObjectProcessor implements ProcessorInterface
{
    use ProcessorTrait;
    use HandleDepthTrait;

    private object $var;

    private string $className;

    private Set $known;

    private int $objectId;

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        $this->var = $this->varDumper->dumpable()->var();
        $this->depth = $this->varDumper->depth() + 1;
        $this->known = $this->varDumper->known();
        $this->className = $this->var::class;
        $this->handleNormalizeClassName();
        $this->objectId = spl_object_id($this->var);
        $this->info = $this->className . '#' . $this->objectId;
    }

    public function type(): string
    {
        return TypeInterface::OBJECT;
    }

    public function write(): void
    {
        $this->varDumper->writer()->write(
            $this->varDumper->format()
                ->getHighlight(
                    VarDumperInterface::CLASS_REG,
                    $this->className
                )
            .
            $this->varDumper->format()
                ->getHighlight(
                    VarDumperInterface::OPERATOR,
                    '#' . strval($this->objectId)
                )
        );
        if ($this->known->contains($this->objectId)) {
            $this->varDumper->writer()->write(
                ' '
                . $this->highlightParentheses(
                    $this->circularReference() . ' #' . $this->objectId
                )
            );

            return;
        }
        if ($this->depth > self::MAX_DEPTH) {
            $this->varDumper->writer()->write(
                ' '
                . $this->highlightParentheses($this->maxDepthReached())
            );

            return;
        }
        $this->known[] = $this->objectId;
        $this->setProperties(new ReflectionObject($this->var));
    }

    private function setProperties(ReflectionObject $reflection): void
    {
        $properties = [];
        $properties = $reflection->isInternal()
            ? $this->getPublicProperties()
            : $this->getExternalProperties($reflection);
        $keys = array_keys($properties);
        foreach ($keys as $name) {
            $name = strval($name);
            $prop = $properties[$name];
            $this->processProperty($name, ...$prop);
        }
    }

    private function getPublicProperties(): array
    {
        $properties = json_decode(json_encode($this->var), true) ?? [];
        foreach ($properties as $name => $value) {
            $name = strval($name);
            $properties[$name] = ['public', $value];
        }

        return $properties;
    }

    private function getExternalProperties(
        ReflectionObject $reflection
    ): array {
        $properties = [];
        do {
            foreach ($reflection->getProperties() as $property) {
                $property->setAccessible(true);

                try {
                    $value = $property->getValue($this->var);
                } catch (Throwable $e) {
                    $value = null;
                }
                $properties[$property->getName()] = [
                    implode(
                        ' ',
                        Reflection::getModifierNames($property->getModifiers())
                    ),
                    $value ?? null,
                ];
            }
        } while ($reflection = $reflection->getParentClass());

        return $properties;
    }

    private function processProperty(string $name, string $modifier, $value): void
    {
        $indentString = $this->varDumper->indentString();
        $modifier = $this->varDumper->format()->getHighlight(
            VarDumperInterface::MODIFIERS,
            $modifier
        );
        $variable = $this->varDumper->format()->getHighlight(
            VarDumperInterface::VARIABLE,
            $this->varDumper->format()->getFilterEncodedChars($name)
        );
        $this->varDumper->writer()->write(
            "\n$indentString$modifier $variable "
        );
        $this->handleDepth($value);
    }

    private function handleNormalizeClassName(): void
    {
        if ((new ValidateString($this->className))
            ->isStartingWith(VarDumperInterface::CLASS_ANON)
        ) {
            $this->className = VarDumperInterface::CLASS_ANON;
        }
    }
}
