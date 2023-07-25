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

use Chevere\String\StringValidate;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\ProcessorInterface;
use Chevere\VarDump\Interfaces\ProcessorNestedInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Processors\Traits\HandleDepthTrait;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;
use Reflection;
use ReflectionObject;
use Throwable;

final class ObjectProcessor implements ProcessorInterface, ProcessorNestedInterface
{
    use ProcessorTrait;
    use HandleDepthTrait;

    private object $var;

    private string $className;

    private int $objectId;

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        /** @var object $object */
        $object = $this->varDumper->dumpable()->var();
        $this->var = $object;
        $this->depth = $this->varDumper->depth() + 1;
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
        if ($this->varDumper->knownObjectsId()->find($this->objectId) !== null) {
            $this->varDumper->writer()->write(
                <<<STRING
                 {$this->highlightParentheses(
                    $this->circularReference() . ' #' . $this->objectId
                )}
                STRING
            );

            return;
        }
        if ($this->depth > self::MAX_DEPTH) {
            $this->varDumper->writer()->write(
                <<<STRING
                 {$this->highlightParentheses($this->maxDepthReached())}
                STRING
            );

            return;
        }
        $this->varDumper = $this->varDumper->withKnownObjectsId(
            $this->varDumper->knownObjectsId()->withPush($this->objectId)
        );
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
            /** @var string[] */
            $property = $properties[$name];
            $this->processProperty($name, ...$property);
        }
    }

    /**
     * @return array<string, array<mixed>>
     */
    private function getPublicProperties(): array
    {
        /** @var array<string, array<mixed>> $properties */
        $properties = json_decode(json_encode($this->var) ?: '', true) ?? [];
        foreach ($properties as $name => $value) {
            $name = strval($name);
            $properties[$name] = ['public', $value];
        }

        return $properties;
    }

    /**
     * @return array<string, array<mixed>>
     */
    private function getExternalProperties(
        ReflectionObject $reflection
    ): array {
        $properties = [];
        do {
            foreach ($reflection->getProperties() as $property) {
                try {
                    $value = $property->getValue($this->var);
                } catch (Throwable) {
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

    private function processProperty(string $name, string $modifier, mixed $value): void
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
            "\n{$indentString}{$modifier} {$variable} "
        );
        $this->handleDepth($value);
    }

    private function handleNormalizeClassName(): void
    {
        if ((new StringValidate($this->className))
            ->isStartingWith(VarDumperInterface::CLASS_ANON)
        ) {
            $this->className = VarDumperInterface::CLASS_ANON;
        }
    }
}
