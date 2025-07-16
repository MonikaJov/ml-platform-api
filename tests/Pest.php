<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Illuminate\Support\Facades\File;

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature', 'Unit');

// Dynamically include all test directories within the Modules folder
$moduleTestPaths = glob(__DIR__.'/../Modules/*/tests/*', GLOB_ONLYDIR);

foreach ($moduleTestPaths as $path) {
    uses(
        Tests\TestCase::class,
        Illuminate\Foundation\Testing\RefreshDatabase::class,
    )->in($path);
}

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

expect()->extend('toHaveAnyOfPrefixes', function (array $prefixes) {
    $classes = getClassesInNamespace($this->value);

    foreach ($classes as $class) {
        $className = (new ReflectionClass($class))->getShortName();
        $hasPrefix = false;

        foreach ($prefixes as $prefix) {
            if (str_starts_with($className, $prefix)) {
                $hasPrefix = true;
                break;
            }
        }

        if (! $hasPrefix) {
            expect(false)->toBeTrue();
        }
    }

    return $this;
});

expect()->extend('toHaveAnyOfSuffixes', function (array $suffixes) {
    $classes = getClassesInNamespace($this->value);

    foreach ($classes as $class) {
        $className = (new ReflectionClass($class))->getShortName();
        $hasSuffix = false;

        foreach ($suffixes as $suffix) {
            if (str_ends_with($className, $suffix)) {
                $hasSuffix = true;
                break;
            }
        }

        if (! $hasSuffix) {
            expect(false)->toBeTrue();
        }
    }

    return $this;
});

expect()->extend('toExtendAnyOf', function (array $parentClasses) {
    $classes = getClassesInNamespace($this->value);

    foreach ($classes as $class) {
        $extendsAny = false;

        foreach ($parentClasses as $parentClass) {
            if (is_subclass_of($class, $parentClass)) {
                $extendsAny = true;
                break;
            }
        }

        if (! $extendsAny) {
            expect(false)->toBeTrue();
        }
    }

    return $this;

});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getClassesInNamespace(string $namespace): array
{
    $mappings = getPsr4Mappings();
    $namespacePath = '';

    foreach ($mappings as $prefix => $path) {
        if ($path === 'Modules/') {
            continue;
        }

        if (str_starts_with($namespace, $prefix)) {
            $relativeNamespace = str_replace($prefix, '', $namespace);
            $namespacePath = base_path(mb_trim($path, '/').'/'.str_replace('\\', '/', $relativeNamespace));
            break;
        }
    }

    if (! $namespacePath || ! File::exists($namespacePath)) {
        return [];
    }

    $classes = [];

    foreach (File::allFiles($namespacePath) as $file) {
        $relativePath = $file->getRelativePathname();
        $className = $namespace.'\\'.str_replace(['/', '.php'], ['\\', ''], $relativePath);
        $classes[] = $className;
    }

    return $classes;
}

function getPsr4Mappings(): array
{
    $composerJsonPath = base_path('composer.json');
    $composerConfig = json_decode(file_get_contents($composerJsonPath), true);

    return $composerConfig['autoload']['psr-4'] ?? [];
}
