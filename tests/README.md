# Behavior Tests

This directory contains behavior tests for the PHPStan Test Attributes extension.

## Test Structure

- `fixtures/` - Test code files with various scenarios
- `TestRunner.php` - PHPUnit test runner that executes PHPStan and validates output
- `phpstan-test.neon` - PHPStan configuration for testing
- `phpstan-test-not-checked.neon` - PHPStan configuration for testing unconfigured classes

## Test Scenarios

### ValidService.php
✅ Tests that correctly attributed methods pass validation

### MissingAttributeService.php
❌ Tests that methods without attributes are reported as errors

### MissingFilePathService.php
❌ Tests that Behaviour/Unit attributes without file paths are reported as errors

### InvalidFilePathService.php
❌ Tests that attributes with non-existent file paths are reported as errors

### PathFormatsService.php
✅ Tests that different path formats (relative, @root/) work correctly

### NotCheckedService.php
✅ Tests that classes not in the configuration are not checked

## Running Tests

### Using PHPUnit (Recommended)

```bash
composer test
```

Or directly:

```bash
vendor/bin/phpunit tests/TestRunner.php
```

### Using PHPStan Directly

```bash
composer test:phpstan
```

Or manually:

```bash
vendor/bin/phpstan analyse --configuration=tests/phpstan-test.neon tests/fixtures
```

## Test Fixtures

The test fixtures include:
- Valid PHP classes with proper attributes
- Invalid PHP classes that should trigger errors
- Test files referenced by the attributes (feature files, PHPUnit tests)

## Expected Behavior

- ✅ Valid services with proper attributes should pass
- ❌ Services missing attributes should report errors
- ❌ Services with missing file paths should report errors
- ❌ Services with invalid file paths should report errors
- ✅ Magic methods should be skipped
- ✅ Private methods should be skipped
- ✅ Unconfigured classes should not be checked

