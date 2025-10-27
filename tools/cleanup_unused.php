<?php
/**
 * Project cleanup helper (dry-run by default).
 *
 * Detects potentially unused files in a Laravel project:
 * - Blade views not referenced by view/include/extends/component directives or routes
 * - Controllers not referenced by routes or class references
 * - Models not referenced anywhere
 * - Mail classes not referenced anywhere
 * - Services not referenced anywhere
 * - Public assets (css/js/images) not referenced in blades or PHP
 * - Root maintenance scripts not referenced by any other file (risky)
 *
 * Usage:
 *   php tools/cleanup_unused.php --dry-run            # default
 *   php tools/cleanup_unused.php --delete             # delete safe categories only
 *   php tools/cleanup_unused.php --delete --include-risky   # also delete risky categories
 *   php tools/cleanup_unused.php --report=path.json   # custom report path
 *
 * This is a heuristic tool: always review the report before deleting.
 */

declare(strict_types=1);

final class ProjectUsageAnalyzer
{
    private string $projectRoot;

    /**
     * Directories that should never be scanned.
     *
     * @var array<int, string>
     */
    private array $ignoreDirectories = [
        'vendor',
        'node_modules',
        'storage',
        'bootstrap/cache',
        '.git',
        '.idea',
        '.vscode',
        '.history',
        'database/schema',
    ];

    /**
     * File extensions to index for content search.
     *
     * @var array<int, string>
     */
    private array $indexExtensions = ['php', 'blade.php', 'js', 'css', 'md', 'json', 'ps1', 'bat'];

    /**
     * Map of absolute file path to file content.
     *
     * @var array<string, string>
     */
    private array $filePathToContent = [];

    /**
     * @param string $projectRoot
     */
    public function __construct(string $projectRoot)
    {
        $this->projectRoot = rtrim($projectRoot, DIRECTORY_SEPARATOR);
    }

    public function run(bool $isDelete, bool $includeRisky, ?string $reportPath = null): int
    {
        $this->indexFiles();

        $candidates = [
            'views' => $this->findUnusedViews(),
            'controllers' => $this->findUnusedClasses(
                baseDir: $this->path('app/Http/Controllers'),
                expectedNamespacePrefix: 'App\\Http\\Controllers',
                risky: false
            ),
            'models' => $this->findUnusedClasses(
                baseDir: $this->path('app/Models'),
                expectedNamespacePrefix: 'App\\Models',
                risky: false
            ),
            'mail' => $this->findUnusedClasses(
                baseDir: $this->path('app/Mail'),
                expectedNamespacePrefix: 'App\\Mail',
                risky: false
            ),
            'services' => $this->findUnusedClasses(
                baseDir: $this->path('app/Services'),
                expectedNamespacePrefix: 'App\\Services',
                risky: true // services are often resolved by container or config; mark as risky
            ),
            'assets' => $this->findUnusedPublicAssets(),
            'root_scripts' => $this->findUnusedRootScripts(), // risky by default
        ];

        $report = [
            'generated_at' => date('c'),
            'project_root' => $this->projectRoot,
            'totals' => array_map(fn ($list) => count($list), $candidates),
            'candidates' => $candidates,
            'note' => 'This is a heuristic. Review before deletion. Some items may be used dynamically.'
        ];

        $reportJson = json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($reportPath === null) {
            $defaultReport = $this->path('cleanup_candidates.json');
            @file_put_contents($defaultReport, (string) $reportJson);
            $reportPath = $defaultReport;
        } else {
            @file_put_contents($reportPath, (string) $reportJson);
        }

        echo "\nPotentially unused files (heuristic)\n";
        foreach ($report['totals'] as $category => $count) {
            echo sprintf("- %s: %d\n", $category, $count);
        }
        echo sprintf("\nFull report saved to: %s\n\n", $reportPath);

        if (!$isDelete) {
            echo "Dry-run only. No files were deleted.\n";
            return 0;
        }

        $deleted = [];
        $skipped = [];

        // Decide which categories are safe to delete by default
        $safeCategories = ['views', 'controllers', 'models', 'mail', 'assets'];
        $riskyCategories = ['services', 'root_scripts'];

        foreach ($candidates as $category => $files) {
            $isRiskyCategory = in_array($category, $riskyCategories, true);
            if ($isRiskyCategory && !$includeRisky) {
                foreach ($files as $path) {
                    $skipped[] = ['path' => $path, 'category' => $category, 'reason' => 'risky_category'];
                }
                continue;
            }

            foreach ($files as $path) {
                if (!is_file($path)) {
                    continue;
                }
                $ok = @unlink($path);
                if ($ok) {
                    $deleted[] = ['path' => $path, 'category' => $category];
                } else {
                    $skipped[] = ['path' => $path, 'category' => $category, 'reason' => 'unlink_failed'];
                }
            }
        }

        echo sprintf("Deleted files: %d\n", count($deleted));
        echo sprintf("Skipped files: %d\n", count($skipped));

        $deleteReport = [
            'deleted' => $deleted,
            'skipped' => $skipped,
        ];
        @file_put_contents($this->path('cleanup_deleted.json'), json_encode($deleteReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo sprintf("Deletion report saved to: %s\n", $this->path('cleanup_deleted.json'));

        return 0;
    }

    /**
     * Configure which file types are considered as reference sources.
     * Modes:
     * - all  : default set including docs and scripts (md, ps1, bat, json)
     * - code : only runtime code files (php, blade.php, js, css)
     */
    public function configureReferenceSources(string $mode): void
    {
        $mode = strtolower(trim($mode));
        if ($mode === 'code') {
            $this->indexExtensions = ['php', 'blade.php', 'js', 'css'];
        } else {
            $this->indexExtensions = ['php', 'blade.php', 'js', 'css', 'md', 'json', 'ps1', 'bat'];
        }
    }

    private function indexFiles(): void
    {
        $this->filePathToContent = [];
        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->projectRoot, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($rii as $fileInfo) {
            if (!$fileInfo instanceof SplFileInfo || !$fileInfo->isFile()) {
                continue;
            }

            $relative = $this->relativePath($fileInfo->getPathname());
            if ($this->isIgnoredPath($relative)) {
                continue;
            }

            $ext = $this->detectLogicalExtension($relative);
            if (!in_array($ext, $this->indexExtensions, true)) {
                continue;
            }

            $content = @file_get_contents($fileInfo->getPathname());
            if ($content === false) {
                $content = '';
            }
            $this->filePathToContent[$fileInfo->getPathname()] = $content;
        }
    }

    private function findUnusedViews(): array
    {
        $unused = [];
        foreach ($this->filePathToContent as $path => $_content) {
            if (!str_contains($path, $this->path('resources/views'))) {
                continue;
            }
            if (!str_ends_with($path, '.blade.php')) {
                continue;
            }

            $viewKey = $this->bladePathToViewKey($path);
            if ($viewKey === null) {
                // Could not derive key; skip deletion
                continue;
            }

            $isReferenced = $this->existsInAnyFile([
                "view('{$viewKey}')",
                "view(\"{$viewKey}\")",
                "View::make('{$viewKey}')",
                "View::make(\"{$viewKey}\")",
                "Route::view(", // not enough; combine with key
                "'{$viewKey}')",
                "\"{$viewKey}\")",
                "@include('{$viewKey}')",
                "@include(\"{$viewKey}\")",
                "@extends('{$viewKey}')",
                "@extends(\"{$viewKey}\")",
                "@component('{$viewKey}')",
                "@component(\"{$viewKey}\")",
            ], $path);

            if (!$isReferenced) {
                $unused[] = $path;
            }
        }
        return $unused;
    }

    private function findUnusedClasses(string $baseDir, string $expectedNamespacePrefix, bool $risky): array
    {
        $unused = [];
        if (!is_dir($baseDir)) {
            return $unused;
        }

        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($rii as $fileInfo) {
            if (!$fileInfo instanceof SplFileInfo || !$fileInfo->isFile()) {
                continue;
            }
            if (!str_ends_with($fileInfo->getFilename(), '.php')) {
                continue;
            }

            $path = $fileInfo->getPathname();
            $content = @file_get_contents($path) ?: '';
            $namespace = $this->extractNamespace($content);
            $className = $this->extractClassName($content);

            if ($className === null) {
                continue;
            }
            $fqcn = ($namespace ? $namespace . '\\' : '') . $className;

            if ($namespace === null || !str_starts_with($fqcn, $expectedNamespacePrefix . '\\')) {
                // Unexpected namespace; consider risky and skip deletion decision
                continue;
            }

            $searchTokens = [
                $fqcn, // Fully qualified name usage
                $className . '::class',
                'new ' . $className . '(',
                $className . '::', // static calls
                "'" . $className . "@", // older route('Controller@method')
            ];

            $isReferenced = $this->existsInAnyFile($searchTokens, $path);
            if (!$isReferenced) {
                $unused[] = $path;
            }
        }
        return $unused;
    }

    private function findUnusedPublicAssets(): array
    {
        $unused = [];
        $publicDir = $this->path('public');
        if (!is_dir($publicDir)) {
            return $unused;
        }

        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($publicDir, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($rii as $fileInfo) {
            if (!$fileInfo instanceof SplFileInfo || !$fileInfo->isFile()) {
                continue;
            }
            $path = $fileInfo->getPathname();

            // Skip entry points and known essentials
            $relative = $this->relativePath($path);
            if (in_array($relative, ['public/index.php', 'public/.htaccess'], true)) {
                continue;
            }

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $whitelistExt = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp', 'bmp', 'json'];
            if (!in_array($ext, $whitelistExt, true)) {
                continue;
            }

            $relativeForSearch = str_replace('\\', '/', $relative);
            $basename = basename($path);

            $searchTokens = [
                $relativeForSearch,
                '/' . ltrim($relativeForSearch, '/'),
                $basename, // fallback: look for filename usage
            ];

            $isReferenced = $this->existsInAnyFile($searchTokens, $path);
            if (!$isReferenced) {
                $unused[] = $path;
            }
        }
        return $unused;
    }

    private function findUnusedRootScripts(): array
    {
        $unused = [];
        $dir = $this->projectRoot;
        $dh = opendir($dir);
        if ($dh === false) {
            return $unused;
        }
        while (($entry = readdir($dh)) !== false) {
            $path = $dir . DIRECTORY_SEPARATOR . $entry;
            if (!is_file($path)) {
                continue;
            }
            // Candidates: standalone maintenance scripts or docs at project root
            $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            $allowed = ['php', 'bat', 'ps1', 'cmd'];
            if (!in_array($ext, $allowed, true)) {
                continue;
            }
            // Skip core files
            $core = ['artisan', 'composer.json', 'composer.lock', 'phpunit.xml'];
            if (in_array($entry, $core, true)) {
                continue;
            }

            $isReferenced = $this->existsInAnyFile([
                $entry,
            ], $path);

            if (!$isReferenced) {
                $unused[] = $path;
            }
        }
        closedir($dh);
        return $unused;
    }

    private function extractNamespace(string $phpContent): ?string
    {
        if (preg_match('/^\s*namespace\s+([^;]+);/m', $phpContent, $m) === 1) {
            return trim($m[1]);
        }
        return null;
    }

    private function extractClassName(string $phpContent): ?string
    {
        if (preg_match('/^\s*(?:final\s+|abstract\s+)?class\s+(\w+)/m', $phpContent, $m) === 1) {
            return trim($m[1]);
        }
        return null;
    }

    private function existsInAnyFile(array $needles, string $excludePath = ''): bool
    {
        foreach ($this->filePathToContent as $path => $content) {
            if ($excludePath !== '' && $path === $excludePath) {
                continue;
            }
            foreach ($needles as $needle) {
                if ($needle === '') {
                    continue;
                }
                if (str_contains($content, $needle)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function bladePathToViewKey(string $absolutePath): ?string
    {
        $viewsRoot = $this->path('resources/views');
        if (!str_starts_with($absolutePath, $viewsRoot)) {
            return null;
        }
        $relative = substr($absolutePath, strlen($viewsRoot) + 1);
        if (!str_ends_with($relative, '.blade.php')) {
            return null;
        }
        $relative = substr($relative, 0, -strlen('.blade.php'));
        $relative = str_replace(['\\', '/'], '.', $relative);
        return $relative;
    }

    private function isIgnoredPath(string $relativePath): bool
    {
        $normalized = str_replace('\\', '/', $relativePath);
        foreach ($this->ignoreDirectories as $ignored) {
            $ignored = trim($ignored, '/');
            if ($ignored === '') {
                continue;
            }
            if ($normalized === $ignored || str_starts_with($normalized, $ignored . '/')) {
                return true;
            }
        }
        return false;
    }

    private function detectLogicalExtension(string $relativePath): string
    {
        if (str_ends_with($relativePath, '.blade.php')) {
            return 'blade.php';
        }
        return strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
    }

    private function relativePath(string $absolutePath): string
    {
        $root = $this->projectRoot . DIRECTORY_SEPARATOR;
        if (str_starts_with($absolutePath, $root)) {
            return str_replace('\\', '/', substr($absolutePath, strlen($root)));
        }
        return str_replace('\\', '/', $absolutePath);
    }

    private function path(string $relative): string
    {
        return $this->projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
    }
}

// -------- Entry point --------

function parseArgFlag(string $flag): bool
{
    foreach ($GLOBALS['argv'] as $arg) {
        if ($arg === $flag) {
            return true;
        }
    }
    return false;
}

function parseArgValue(string $prefix, ?string $default = null): ?string
{
    foreach ($GLOBALS['argv'] as $arg) {
        if (str_starts_with($arg, $prefix)) {
            $parts = explode('=', $arg, 2);
            return $parts[1] ?? $default;
        }
    }
    return $default;
}

// Ensure script runs from project root
$projectRoot = realpath(__DIR__ . '/..') ?: getcwd();
$analyzer = new ProjectUsageAnalyzer($projectRoot);

$isDelete = parseArgFlag('--delete');
$includeRisky = parseArgFlag('--include-risky');
$reportPath = parseArgValue('--report=');
$referenceMode = parseArgValue('--reference-sources=', 'all');

$analyzer->configureReferenceSources($referenceMode ?? 'all');

exit($analyzer->run($isDelete, $includeRisky, $reportPath));

