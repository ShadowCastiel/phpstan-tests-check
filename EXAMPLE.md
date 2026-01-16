# Usage Examples

## Example Service Class

```php
<?php

namespace App\Service;

use ShadowCastiel\PHPStan\TestsCheck\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\Unit;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\NoTest;

class UserService
{
    // ✅ Valid - has Behaviour attribute with relative path
    #[Behaviour('features/user_creation.feature')]
    public function createUser(string $email, string $name): User
    {
        // Implementation
    }

    // ✅ Valid - has Unit attribute with project root relative path
    #[Unit('@root/tests/Unit/UserServiceTest.php')]
    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // ✅ Valid - has Unit attribute with relative path
    #[Unit('tests/Unit/EmailValidationTest.php')]
    public function validateEmailFormat(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // ✅ Valid - has NoTest attribute with description
    #[NoTest('Simple getter that returns configuration array, no business logic to test')]
    public function getConfig(): array
    {
        return ['version' => '1.0'];
    }

    // ❌ Invalid - missing required attribute
    // PHPStan will report: "Public method UserService::deleteUser() must have one of the following attributes..."
    public function deleteUser(int $userId): void
    {
        // Implementation
    }

    // ❌ Invalid - NoTest attribute missing description
    // PHPStan will report: "Attribute NoTest on method UserService::updateUser() requires a description parameter."
    #[NoTest]
    public function updateUser(int $userId, array $data): void
    {
        // Implementation
    }

    // ❌ Invalid - NoTest attribute with empty description
    // PHPStan will report: "Attribute NoTest on method UserService::helper() requires a non-empty description..."
    #[NoTest('')]
    public function helper(): void
    {
        // Implementation
    }

    // ❌ Invalid - file path doesn't exist
    // PHPStan will report: "File path specified in Unit attribute on method UserService::deleteUser() does not exist: ..."
    #[Unit('tests/Unit/NonExistentTest.php')]
    public function deleteUserWithInvalidPath(int $userId): void
    {
        // Implementation
    }

    // ✅ Valid - private methods are not checked
    private function internalHelper(): void
    {
        // Implementation
    }

    // ✅ Valid - most magic methods are automatically skipped
    public function __construct()
    {
        // Implementation
    }

    // ✅ Valid - __invoke requires an attribute (it's not skipped)
    #[Behaviour('features/invoke.feature')]
    public function __invoke(): void
    {
        // Implementation
    }
}
```

## Configuration Example

In your `phpstan.neon`:

```neon
parameters:
    testAttributes:
        checkedClasses:
            # Check all classes ending with "Service"
            - '*Service'
            
            # Check all classes ending with "UseCase"
            - '*UseCase'
            
            # Check specific classes
            - 'App\\Handler\\EmailHandler'
            
            # Check all classes implementing an interface
            - 'App\\Contracts\\ServiceInterface'
```

## Using Short Attribute Names

You can also use short names if you import them:

```php
<?php

use ShadowCastiel\PHPStan\TestsCheck\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\Unit;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\NoTest;

class MyService
{
    #[Behaviour('features/my_feature.feature')]  // ✅ Works with short name
    public function doSomething(): void
    {
    }
}
```

## File Path Examples

### Class-Strings (Recommended for IDEs)

**Best practice**: Use `::class` syntax for full IDE support (navigation, refactoring, autocomplete):

```php
use Tests\Unit\UserServiceTest;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\Unit;

class UserService
{
    // ✨ Best: PHPStorm recognizes this and provides full IDE support
    #[Unit(UserServiceTest::class)]
    public function validateEmail(): bool {}
}
```

### Relative Paths

Paths are relative to the file being analyzed:

```php
// If this file is at: src/Service/UserService.php
// The path below resolves to: src/Service/features/user_creation.feature
#[Behaviour('features/user_creation.feature')]
public function createUser(): void {}
```

### Project Root Relative Paths

Use `@root/` prefix for paths relative to the project root (where `composer.json` is located):

```php
// Resolves to: /path/to/project/tests/Unit/UserServiceTest.php
#[Unit('@root/tests/Unit/UserServiceTest.php')]
public function validateEmail(): bool {}
```

### Absolute Paths

You can also use absolute paths:

```php
#[Unit('/var/www/project/tests/Unit/UserServiceTest.php')]
public function validateEmail(): bool {}
```

## Magic Methods

By default, **all public methods** (including magic methods) are checked and require attributes. To exclude magic methods, configure the `excludedMethods` parameter:

```neon
services:
    -
        class: ShadowCastiel\PHPStan\TestsCheck\Rule\TestAttributeRule
        arguments:
            checkedClassPatterns:
                - '*Service'
            excludedMethods:
                - '__construct'
                - '__destruct'
                - '__toString'
                # Add other methods you want to exclude
```

### Example with Excluded Methods

```php
// With excludedMethods configured to exclude __construct
class MyService
{
    // ✅ Valid - __construct is excluded
    public function __construct()
    {
    }

    // ❌ Invalid - __invoke not excluded, requires attribute
    public function __invoke(): void
    {
    }

    // ✅ Valid - has Behaviour attribute
    #[Behaviour('features/invoke.feature')]
    public function __invoke(): void
    {
    }
}
```

### Configuring Excluded Methods

You can customize which methods are excluded by configuring the `excludedMethods` parameter:

```neon
services:
    -
        class: ShadowCastiel\PHPStan\TestsCheck\Rule\TestAttributeRule
        arguments:
            checkedClassPatterns:
                - '*Service'
            excludedMethods:
                - '__construct'
                - '__destruct'
                - 'myCustomHelper'  # Exclude your own methods
```

**Note**: By default, `excludedMethods` is an empty array `[]`, meaning all public methods will be checked.

## IDE Support - Clickable File Paths

**File paths in attributes are automatically clickable in modern IDEs!**

### Best Practice: Use Class-Strings

For **optimal PHPStorm integration**, use the `::class` syntax:

```php
use Tests\Unit\UserServiceTest;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\Unit;

class UserService
{
    #[Unit(UserServiceTest::class)]
    //     ^^^^^^^^^^^^^^^^^^^^^^^
    //     ✨ PHPStorm provides:
    //     - Navigate to test class (Ctrl/Cmd + Click)
    //     - Refactoring support (rename, move)
    //     - Autocomplete when typing
    //     - Find usages
    public function validateEmail(): bool {}
}
```

### Alternative: String Paths

In PhpStorm, VS Code (with PHP extensions), and other modern IDEs, string paths are also clickable:

- **Click directly on file paths** in attribute parameters to navigate to the test files
- **Use Ctrl/Cmd + Click** (or Cmd + Click on Mac) on the path string to open the file
- **Hover over paths** to see file information
- **Use "Go to Declaration"** or similar IDE features

Example:
```php
#[Unit('@root/tests/Unit/UserServiceTest.php')]
//     ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//     Click here in your IDE to open the test file!
public function validateEmail(): bool {}
```

For dynamic paths, use the `TestPath` helper:

```php
use ShadowCastiel\PHPStan\TestsCheck\TestPath;
use ShadowCastiel\PHPStan\TestsCheck\Attribute\Unit;

class UserService
{
    #[Unit(TestPath::root('tests/Unit/UserServiceTest.php'))]
    public function validateEmail(): bool {}
}
```

