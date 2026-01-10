<?php

declare(strict_types=1);

namespace ShadowCastiel\PHPStan\TestAttributes;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Type;
use stdClass;

/**
 * PHPStan extension for test attributes.
 *
 * @psalm-api
 */
class TestAttributesExtension implements
    DynamicMethodReturnTypeExtension,
    DynamicStaticMethodReturnTypeExtension,
    DynamicFunctionReturnTypeExtension
{
    /**
     * @return class-string<stdClass>
     */
    public function getClass(): string
    {
        return stdClass::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return false;
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope,
    ): ?Type {
        return null;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return false;
    }

    public function getTypeFromStaticMethodCall(
        MethodReflection $methodReflection,
        StaticCall $methodCall,
        Scope $scope,
    ): ?Type {
        return null;
    }

    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return false;
    }

    public function getTypeFromFunctionCall(
        FunctionReflection $functionReflection,
        FuncCall $functionCall,
        Scope $scope,
    ): ?Type {
        return null;
    }
}
