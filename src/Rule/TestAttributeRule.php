<?php

declare(strict_types=1);

namespace ShadowCastiel\PHPStan\TestsCheck\Rule;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Throwable;

/**
 * PHPStan rule to check test attributes on public methods.
 *
 * @psalm-api
 *
 * @implements Rule<Node\Stmt\ClassMethod>
 */
class TestAttributeRule implements Rule
{
    /** @var string[] */
    private array $checkedClassPatterns;

    /** @var string[] */
    private array $excludedMethods;

    /**
     * @param string[] $checkedClassPatterns Class names or patterns to check
     * @param string[] $excludedMethods Method names to exclude from checking.
     *                                  [] = exclude nothing (check all), [names] = exclude only these
     */
    public function __construct(array $checkedClassPatterns = [], array $excludedMethods = [])
    {
        $this->checkedClassPatterns = $checkedClassPatterns;
        $this->excludedMethods = $excludedMethods;
    }

    public function getNodeType(): string
    {
        return Node\Stmt\ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof Node\Stmt\ClassMethod) {
            return [];
        }

        if (!$node->isPublic()) {
            return [];
        }

        if ($this->isExcludedMethod($node->name->name)) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        if (!$this->shouldCheckClass($classReflection)) {
            return [];
        }

        $errors = [];
        $attributeInfo = $this->getRequiredAttribute($node, $scope);

        if ($attributeInfo === null) {
            return [
                RuleErrorBuilder::message(
                    \sprintf(
                        'Public method %s::%s() must have one of the following attributes: %s, %s, or %s.',
                        $classReflection->getName(),
                        $node->name->name,
                        'ShadowCastiel\\PHPStan\\TestsCheck\\Attribute\\Behaviour',
                        'ShadowCastiel\\PHPStan\\TestsCheck\\Attribute\\Unit',
                        'ShadowCastiel\\PHPStan\\TestsCheck\\Attribute\\NoTest',
                    ),
                )->identifier('shadowcastiel.testsCheck.missingAttribute')
                ->line($node->getStartLine())
                ->build(),
            ];
        }

        if ($attributeInfo['type'] === 'NoTest') {
            $description = $attributeInfo['description'] ?? null;

            if ($description === null) {
                $errors[] = RuleErrorBuilder::message(
                    \sprintf(
                        'Attribute NoTest on method %s::%s() requires a description parameter.',
                        $classReflection->getName(),
                        $node->name->name,
                    ),
                )->identifier('shadowcastiel.testsCheck.missingDescription')
                ->line($node->getStartLine())
                ->build();
            } elseif (trim($description) === '') {
                $errors[] = RuleErrorBuilder::message(
                    \sprintf(
                        'Attribute NoTest on method %s::%s() requires a non-empty description explaining why this method does not require a test.',
                        $classReflection->getName(),
                        $node->name->name,
                    ),
                )->identifier('shadowcastiel.testsCheck.emptyDescription')
                ->line($node->getStartLine())
                ->build();
            }
        }

        if ($attributeInfo['type'] === 'Behaviour' || $attributeInfo['type'] === 'Unit') {
            $filePath = $attributeInfo['filePath'] ?? null;

            if ($filePath === null) {
                $errors[] = RuleErrorBuilder::message(
                    \sprintf(
                        'Attribute %s on method %s::%s() requires a filePath parameter.',
                        $attributeInfo['type'],
                        $classReflection->getName(),
                        $node->name->name,
                    ),
                )->identifier('shadowcastiel.testsCheck.missingFilePath')
                ->line($node->getStartLine())
                ->build();
            } else {
                $resolvedPath = $this->resolveFilePath($filePath, $scope);
                $fileExists = $resolvedPath !== null && file_exists($resolvedPath);

                if (!$fileExists) {
                    $errorMessage = \sprintf(
                        'File path specified in %s attribute on method %s::%s() does not exist: %s',
                        $attributeInfo['type'],
                        $classReflection->getName(),
                        $node->name->name,
                        $filePath,
                    );

                    if ($resolvedPath !== null) {
                        $errorMessage .= \sprintf(' (resolved to: %s)', $resolvedPath);
                    } else {
                        $errorMessage .= ' (could not resolve path)';
                    }

                    $errorBuilder = RuleErrorBuilder::message($errorMessage)
                        ->identifier('shadowcastiel.testsCheck.invalidFilePath')
                        ->line($node->getStartLine())
                        ->file($scope->getFile());

                    if ($resolvedPath !== null) {
                        $errorBuilder->tip(\sprintf('Expected file: %s', $resolvedPath));
                    }

                    $errors[] = $errorBuilder->build();
                }
            }
        }

        return $errors;
    }

    private function shouldCheckClass(ClassReflection $classReflection): bool
    {
        if (empty($this->checkedClassPatterns)) {
            return false;
        }

        $className = $classReflection->getName();
        $shortClassName = basename(str_replace('\\', '/', $className));

        foreach ($this->checkedClassPatterns as $pattern) {
            if (str_contains($pattern, '*')) {
                $regex = '/^' . str_replace(['\\*', '\\'], ['.*', '\\\\'], $pattern) . '$/';
                if (preg_match($regex, $className) || preg_match($regex, $shortClassName)) {
                    return true;
                }
            } elseif ($className === $pattern || $shortClassName === $pattern) {
                return true;
            }

            // Check if class implements interface or is a subclass (using is() which covers both)
            if ($classReflection->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{type: string, filePath: string|null, description: string|null}|null
     */
    private function getRequiredAttribute(Node\Stmt\ClassMethod $node, Scope $scope): ?array
    {
        $requiredAttributes = [
            'ShadowCastiel\\PHPStan\\TestsCheck\\Attribute\\Behaviour',
            'ShadowCastiel\\PHPStan\\TestsCheck\\Attribute\\Unit',
            'ShadowCastiel\\PHPStan\\TestsCheck\\Attribute\\NoTest',
        ];

        $requiredAttributeShortNames = ['Behaviour', 'Unit', 'NoTest'];

        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $attrName = $this->getAttributeName($attr);
                $matchedType = null;

                if (\in_array($attrName, $requiredAttributes, true)) {
                    $matchedType = basename(str_replace('\\', '/', $attrName));
                } else {
                    foreach ($requiredAttributeShortNames as $shortName) {
                        if ($attrName === $shortName || str_ends_with($attrName, '\\' . $shortName)) {
                            $matchedType = $shortName;
                            break;
                        }
                    }
                }

                if ($matchedType !== null) {
                    $filePath = $this->extractFilePathFromAttribute($attr, $matchedType, $scope);
                    $description = $this->extractDescriptionFromAttribute($attr, $matchedType);
                    return [
                        'type' => $matchedType,
                        'filePath' => $filePath,
                        'description' => $description,
                    ];
                }
            }
        }

        return null;
    }

    private function extractFilePathFromAttribute(Attribute $attr, string $attributeType, Scope $scope): ?string
    {
        if ($attributeType === 'NoTest') {
            return null;
        }

        if (empty($attr->args)) {
            return null;
        }

        $firstArg = $attr->args[0];

        // Handle regular string literals
        if ($firstArg->value instanceof Node\Scalar\String_) {
            return $firstArg->value->value;
        }

        // Handle string concatenation
        if ($firstArg->value instanceof Node\Expr\BinaryOp\Concat) {
            return $this->extractStringFromConcat($firstArg->value);
        }

        // Handle class-string (e.g., Test::class)
        if ($firstArg->value instanceof Node\Expr\ClassConstFetch) {
            return $this->extractFilePathFromClassConst($firstArg->value, $scope);
        }

        return null;
    }

    private function extractDescriptionFromAttribute(Attribute $attr, string $attributeType): ?string
    {
        if ($attributeType !== 'NoTest') {
            return null;
        }

        if (empty($attr->args)) {
            return null;
        }

        $firstArg = $attr->args[0];

        // Handle regular string literals
        if ($firstArg->value instanceof Node\Scalar\String_) {
            return $firstArg->value->value;
        }

        // Handle string concatenation
        if ($firstArg->value instanceof Node\Expr\BinaryOp\Concat) {
            return $this->extractStringFromConcat($firstArg->value);
        }

        return null;
    }

    private function extractFilePathFromClassConst(Node\Expr\ClassConstFetch $classConst, Scope $scope): ?string
    {
        // Only handle ::class constants
        if (!$classConst->name instanceof Node\Identifier || $classConst->name->name !== 'class') {
            return null;
        }

        // Get the class name
        if ($classConst->class instanceof Node\Name) {
            $className = $classConst->class->toString();

            // Try to resolve the class using PHPStan's reflection
            try {
                // Handle relative class names (resolve imports)
                if (!str_contains($className, '\\')) {
                    // Try to resolve via scope
                    $resolvedName = $scope->resolveName($classConst->class);
                    $className = $resolvedName;
                }

                if ($scope->hasClass($className)) {
                    $classReflection = $scope->getClassReflection($className);
                    if ($classReflection !== null) {
                        $fileName = $classReflection->getFileName();
                        if ($fileName !== null) {
                            return $fileName;
                        }
                    }
                }
            } catch (Throwable $e) {
                // If reflection fails, return null
                return null;
            }
        }

        return null;
    }

    private function extractStringFromConcat(Node\Expr\BinaryOp\Concat $concat): ?string
    {
        $left = $concat->left instanceof Node\Scalar\String_
            ? $concat->left->value
            : ($concat->left instanceof Node\Expr\BinaryOp\Concat
                ? $this->extractStringFromConcat($concat->left)
                : null);

        $right = $concat->right instanceof Node\Scalar\String_
            ? $concat->right->value
            : ($concat->right instanceof Node\Expr\BinaryOp\Concat
                ? $this->extractStringFromConcat($concat->right)
                : null);

        if ($left === null || $right === null) {
            return null;
        }

        return $left . $right;
    }

    private function resolveFilePath(string $filePath, Scope $scope): ?string
    {
        if (str_starts_with($filePath, '@root/')) {
            $projectRoot = $this->getProjectRoot($scope);
            if ($projectRoot === null) {
                return null;
            }
            $relativePath = substr($filePath, 6);
            return rtrim($projectRoot, '/') . '/' . ltrim($relativePath, '/');
        }

        if (str_starts_with($filePath, '/')) {
            return $filePath;
        }

        $filePathFromScope = $scope->getFile();
        $fileDirectory = \dirname($filePathFromScope);
        return rtrim($fileDirectory, '/') . '/' . ltrim($filePath, '/');
    }

    private function getProjectRoot(Scope $scope): ?string
    {
        $filePath = $scope->getFile();
        $currentDir = \dirname($filePath);
        $maxDepth = 10;
        $depth = 0;

        while ($depth < $maxDepth) {
            $composerJson = $currentDir . '/composer.json';
            if (file_exists($composerJson)) {
                return $currentDir;
            }

            $parentDir = \dirname($currentDir);
            if ($parentDir === $currentDir) {
                break;
            }

            $currentDir = $parentDir;
            $depth++;
        }

        return getcwd() ?: null;
    }

    private function getAttributeName(Attribute $attr): string
    {
        return $attr->name->toString();
    }

    private function isExcludedMethod(string $methodName): bool
    {
        return \in_array($methodName, $this->excludedMethods, true);
    }
}
