# PHPStan Test Attributes Extension

A PHPStan extension that enforces public methods in services, use cases, and other configured classes to have one of the required test attributes: `Behaviour`, `Unit`, or `NoTest`.

## Installation

### Via Composer

```bash
composer require --dev shadowcastiel/phpstan-tests-check
```

If you have [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer) installed, the extension will be automatically registered. Otherwise, you need to include it manually in your `phpstan.neon`:

```neon
includes:
    - vendor/shadowcastiel/phpstan-tests-check/extension.neon
```

### Via PHPStan Extension Manager

This extension is compatible with PHPStan's extension manager. Once installed via Composer, it will be automatically detected and registered if you have the extension installer.

## Features

- Enforces that public methods in configured classes have one of the required attributes:
  - `ShadowCastiel\PHPStan\TestAttributes\Attribute\Behaviour` - Marks a method as requiring a behaviour test (requires file path to feature file)
  - `ShadowCastiel\PHPStan\TestAttributes\Attribute\Unit` - Marks a method as requiring a unit test (requires file path to PHPUnit test file)
  - `ShadowCastiel\PHPStan\TestAttributes\Attribute\NoTest` - Marks a method as not requiring a test
- **File path validation**: Validates that the specified test files exist
- **Multiple path formats supported**:
  - Relative paths (relative to the file being analyzed)
  - Absolute paths
  - Project root relative paths (using `@root/` prefix)
- Configurable class patterns (wildcards, interfaces, full class names)
- Automatically skips most magic methods (e.g., `__construct`, `__toString`) - **Note**: `__invoke` is checked and requires attributes

## Usage

### 1. Use the Attributes

Add one of the three attributes to your public methods. **Note**: `Behaviour` and `Unit` attributes require a file path parameter:

```php
use ShadowCastiel\PHPStan\TestAttributes\Attribute\Behaviour;
use ShadowCastiel\PHPStan\TestAttributes\Attribute\Unit;
use ShadowCastiel\PHPStan\TestAttributes\Attribute\NoTest;

class UserService
{
    // Using relative path (relative to the current file)
    #[Behaviour('features/user_creation.feature')]
    public function createUser(string $email): User
    {
        // This method requires a behaviour test
    }

    // Using project root relative path (starts with @root/)
    #[Unit('@root/tests/Unit/UserServiceTest.php')]
    public function validateEmail(string $email): bool
    {
        // This method requires a unit test
    }

    // Using absolute path
    #[Unit('/var/www/project/tests/Unit/EmailValidationTest.php')]
    public function validateEmailFormat(string $email): bool
    {
        // This method requires a unit test
    }

    // NoTest doesn't require a file path
    #[NoTest]
    public function getConfig(): array
    {
        // This method doesn't require a test
    }
}
```

#### File Path Formats

The `Behaviour` and `Unit` attributes accept file paths in three formats:

1. **Relative paths** (relative to the file being analyzed):
   ```php
   #[Behaviour('features/user_creation.feature')]
   #[Unit('tests/Unit/UserServiceTest.php')]
   ```

2. **Project root relative paths** (using `@root/` prefix):
   ```php
   #[Behaviour('@root/features/user_creation.feature')]
   #[Unit('@root/tests/Unit/UserServiceTest.php')]
   ```
   The project root is automatically detected by looking for `composer.json`.

3. **Absolute paths**:
   ```php
   #[Behaviour('/var/www/project/features/user_creation.feature')]
   #[Unit('/var/www/project/tests/Unit/UserServiceTest.php')]
   ```

PHPStan will validate that the specified files exist and report an error if they don't.

#### Magic Methods

By default, **all public methods** (including magic methods) are checked and require attributes. You can configure which methods to exclude using the `excludedMethods` parameter in your PHPStan configuration:

```neon
excludedMethods:
    - '__construct'
    - '__destruct'
    # Add other methods you want to exclude
```

See the "Configuring Excluded Methods" section below for more details.

#### IDE Support

**File paths in attributes are automatically clickable in modern IDEs** (PhpStorm, VS Code with PHP extensions, etc.). You can:

- **Click on file paths** in attribute parameters to navigate directly to the test files
- **Use Ctrl/Cmd + Click** (or Cmd + Click on Mac) on the path string to open the file
- **See file references** in IDE tooltips and autocomplete

For dynamic path construction, you can use the `TestPath` helper class:

```php
use ShadowCastiel\PHPStan\TestAttributes\TestPath;
use ShadowCastiel\PHPStan\TestAttributes\Attribute\Unit;

class UserService
{
    #[Unit(TestPath::root('tests/Unit/UserServiceTest.php'))]
    public function validateEmail(): bool {}
}
```

### 2. Configure Which Classes to Check

In your `phpstan.neon`, override the service to configure which classes should be checked:

```neon
includes:
    - vendor/shadowcastiel/phpstan-test-attributes/extension.neon

services:
    -
        class: ShadowCastiel\PHPStan\TestAttributes\Rule\TestAttributeRule
        arguments:
            checkedClassPatterns:
                # Using wildcard patterns
                - '*Service'
                - '*UseCase'
                - '*Handler'
                
                # Using full class names
                - 'App\\Service\\UserService'
                - 'App\\UseCase\\CreateUserUseCase'
                
                # Using interface names (all classes implementing this interface will be checked)
                - 'App\\Contracts\\ServiceInterface'
            excludedMethods:
                # Methods to exclude from checking (optional)
                # If not specified, defaults to common magic methods (except __invoke)
                - '__construct'
                - '__destruct'
                # Add your custom methods to exclude here
                - 'customHelper'
```

By default, no classes are checked. You must override the service to enable the rule.

#### Configuring Excluded Methods

By default, `excludedMethods` is an empty array `[]`, meaning **all public methods will be checked** (including magic methods and `__invoke`).

To exclude specific methods from the rule check, provide the `excludedMethods` argument:

```neon
services:
    -
        class: ShadowCastiel\PHPStan\TestAttributes\Rule\TestAttributeRule
        arguments:
            checkedClassPatterns:
                - '*Service'
            excludedMethods:
                - '__construct'     # Exclude constructor
                - '__destruct'      # Exclude destructor
                - '__toString'      # Exclude other magic methods
                - 'myCustomMethod'  # Exclude your own methods
```

**Common magic methods to exclude**:
- `__construct`, `__destruct`, `__call`, `__callStatic`
- `__get`, `__set`, `__isset`, `__unset`
- `__sleep`, `__wakeup`, `__toString`, `__set_state`, `__clone`, `__debugInfo`, `__invoke`

## Requirements

- PHP >= 8.1
- PHPStan 2.0 or higher

## Development

### Running Tests

The extension includes comprehensive behavior tests:

```bash
# Run all tests with PHPUnit
composer test

# Run PHPStan directly on test fixtures
composer test:phpstan
```

See [tests/README.md](tests/README.md) for more details about the test structure.

### Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

MIT

