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

use Chevere\Parameter\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\ProcessorInterface;
use Chevere\VarDump\Interfaces\ProcessorNestedInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Processors\Traits\HandleDepthTrait;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;
use Reflection;
use ReflectionObject;

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
                ->highlight(
                    VarDumperInterface::CLASS_REG,
                    $this->className
                )
            .
            $this->varDumper->format()
                ->highlight(
                    VarDumperInterface::OPERATOR,
                    '#' . strval($this->objectId)
                )
        );
        if ($this->varDumper->objectReferences()->has($this->objectId)) {
            $this->varDumper->writer()->write(
                <<<STRING
                 {$this->highlightParentheses(
                    $this->circularReference() . ' #' . $this->objectId
                )}
                STRING
            );

            return;
        }
        $this->varDumper->objectReferences()->push($this->objectId);
        if ($this->depth > self::MAX_DEPTH) {
            $this->varDumper->writer()->write(
                <<<STRING
                 {$this->highlightParentheses($this->maxDepthReached())}
                STRING
            );

            return;
        }
        $this->setProperties($this->var);
    }

    private function setProperties(object $object): void
    {
        $reflection = new ReflectionObject($object);
        $properties = [];
        $properties = $this->getProperties($object, $reflection);
        // @codeCoverageIgnoreStart
        if ($properties === [] && $reflection->isInternal()) {
            $properties = $this->getPublicProperties();
        }
        // @codeCoverageIgnoreEnd
        $keys = array_keys($properties);
        $aux = 0;
        foreach ($keys as $name) {
            $aux++;
            $name = strval($name);
            /** @var string[] */
            $property = $properties[$name];
            $property[] = $aux;
            // @phpstan-ignore-next-line
            $this->processProperty($name, ...$property);
        }
        if ($aux > 0) {
            $this->varDumper->writer()->write(
                $this->varDumper->format()->detailsClose()
            );
            $this->varDumper = $this->varDumper->withNeedsPull(true);
        }
    }

    /**
     * @return array<string, array<mixed>>
     * @codeCoverageIgnore
     */
    private function getPublicProperties(): array
    {
        /** @var array<string, array<mixed>> $properties */
        $properties = json_decode(json_encode($this->var) ?: '', true) ?? [];
        foreach ($properties as $name => $value) {
            $name = strval($name);
            $properties[$name] = ['public', $value, false];
        }

        return $properties;
    }

    /**
     * @return array<string, array<mixed>>
     */
    private function getProperties(
        object $object,
        ReflectionObject $reflection
    ): array {
        $properties = [];
        do {
            foreach ($reflection->getProperties() as $property) {
                if ($property->isStatic()) {
                    continue;
                }
                $isUnset = false;
                if (! $property->isInitialized($object)) {
                    if ($property->hasDefaultValue()) {
                        $value = $property->getDefaultValue();
                    } else {
                        $value = '';
                        $isUnset = true;
                    }
                } else {
                    $value = $property->getValue($this->var);
                }
                $properties[$property->getName()] = [
                    implode(
                        ' ',
                        Reflection::getModifierNames($property->getModifiers())
                    ),
                    $value ?? null,
                    $isUnset,
                ];
            }
        } while ($reflection = $reflection->getParentClass());

        return $properties;
    }

    private function processProperty(
        string $name,
        string $modifier,
        mixed $value,
        bool $isUnset,
        int $aux
    ): void {
        if ($aux === 1) {
            $open = $this->varDumper->depth() === 0;
            $this->varDumper->writer()->write(
                $this->varDumper->format()->detailsOpen($open)
            );
            if ($this->varDumper->format()->detailsClose() === '') {
                $this->varDumper->writer()->write("\n");
            }
        } else {
            if ($this->varDumper->needsPull()) {
                $this->varDumper->writer()->write(
                    $this->varDumper->format()->detailsPullUp()
                );
                $this->varDumper = $this->varDumper->withNeedsPull(false);
            }
            $this->varDumper->writer()->write("\n");
        }
        $indentString = $this->varDumper->indentString();
        $modifier = $this->varDumper->format()->highlight(
            VarDumperInterface::MODIFIERS,
            $modifier
        );
        $variable = $this->varDumper->format()->highlight(
            VarDumperInterface::VARIABLE,
            $this->varDumper->format()->filterEncodedChars($name)
        );
        $this->varDumper->writer()->write(
            "{$indentString}{$modifier} {$variable} "
        );
        if ($isUnset) {
            $unset = $this->varDumper->format()->highlight(
                VarDumperInterface::EMPHASIS,
                'uninitialized'
            );
            $this->varDumper->writer()->write($unset);
        } else {
            $this->handleDepth($value);
        }
    }

    private function handleNormalizeClassName(): void
    {
        if (str_starts_with($this->className, VarDumperInterface::CLASS_ANON)) {
            $this->className = VarDumperInterface::CLASS_ANON;
        }
    }
}
