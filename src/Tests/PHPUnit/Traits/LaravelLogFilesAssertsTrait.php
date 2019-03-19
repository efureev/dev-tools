<?php

declare(strict_types=1);

namespace AvtoDev\DevTools\Tests\PHPUnit\Traits;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Log\LogManager;
use Illuminate\Support\Str;
use PHPUnit\Framework\AssertionFailedError;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Trait LaravelLogFilesAssertsTrait
 * @package AvtoDev\DevTools\Tests\PHPUnit\Traits
 */
trait LaravelLogFilesAssertsTrait
{
    /**
     * @param string $default_log_file_name
     *
     * @return string
     */
    public function fixLaravelLogFileName(string $default_log_file_name): string
    {
        // For Laravel 5.7 and higher
        if (\class_exists(LogManager::class)) {
            /** @var LogManager $logger */
            $logger = $this->app->make(LogManager::class);

            // Currently enabled 'stack' channel
            if ($logger->getDefaultDriver() === ($stack = 'stack')) {
                /** @var ConfigRepository $config */
                $config = $this->app->make('config');

                // Current channel has 'stack' driver
                if ($config->get("logging.channels.{$stack}.driver") === $stack) {
                    // Stack channel is 'daily'
                    if ($config->get("logging.channels.{$stack}.channels") === [$daily = 'daily']) {
                        return Str::replaceLast(
                            '.log',
                            '-' . \date('Y-m-d') . '.log',
                            $default_log_file_name
                        );
                    }
                }
            }
        }

        return $default_log_file_name;
    }

    /**
     * Make logs directory cleaning (remove all files and directories inside).
     *
     * @param string|null $logs_directory_path
     *
     * @return void
     */
    public function clearLaravelLogs($logs_directory_path = null): void
    {
        $fs = new Filesystem;
        $directory_path = $logs_directory_path ?? $this->getDefaultLogsDirectoryPath();

        if (!$fs->isDirectory($directory_path)) {
            return;
        }

        $items = new \FilesystemIterator($directory_path);

        /** @var \SplFileInfo $item */
        foreach ($items as $item) {
            // Recursive directories walking
            if ($item->isDir()) {
                $this->clearLaravelLogs($item->getPathname());
                continue;
            }

            // Skip hidden files and directories
            if (Str::startsWith($item->getFilename(), '.')) {
                continue;
            }

            $fs->delete($item->getPathname());
        }
    }

    /**
     * Assert that log file exists.
     *
     * @param string $file_name
     *
     * @throws AssertionFailedError
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function assertLogFileExists(string $file_name = 'laravel.log'): void
    {
        $this->assertFileExists(
            $this->getDefaultLogsDirectoryPath($file_name),
            "Log file [{$file_name}] does not exists."
        );
    }

    /**
     * Assert that log file NOT exists.
     *
     * @param string $file_name
     *
     * @throws AssertionFailedError
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function assertLogFileNotExists(string $file_name = 'laravel.log'): void
    {
        $this->assertFileNotExists(
            $this->getDefaultLogsDirectoryPath($file_name),
            "Log file [{$file_name}] exists (but should be not)."
        );
    }

    /**
     * Assert that log file contains passed substring.
     *
     * @param string $substring
     * @param string $file
     * @param int|null $lines_limit Make search only in N last log files lines. Pass null to disable this limitation
     *
     * @throws AssertionFailedError
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function assertLogFileContains(string $substring, string $file = 'laravel.log', $lines_limit = null): void
    {
        $lines = $this->getLogFileContentAsArray($file, $lines_limit === null
            ? $lines_limit
            : $lines_limit + 1);

        $this->assertLogFileExists($file);

        $this->assertStringContainsString($substring, \implode("\n", $lines),
            "Log file [{$file}] does not contains [{$substring}].");
    }

    /**
     * Assert that log file NOT contains passed substring.
     *
     * @param string $substring
     * @param string $file
     * @param int|null $lines_limit Make search only in N last log files lines. Pass null to disable this limitation
     *
     * @throws AssertionFailedError
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function assertLogFileNotContains(string $substring, string $file = 'laravel.log', $lines_limit = null): void
    {
        $lines = $this->getLogFileContentAsArray($file, $lines_limit === null
            ? $lines_limit
            : $lines_limit + 1);

        $this->assertStringNotContainsString($substring, \implode("\n", $lines), "Log file [{$file}] contains [{$substring}].");
    }

    /**
     * Get default logs directory path.
     *
     * @param string|null $optional_path
     *
     * @return string
     */
    public function getDefaultLogsDirectoryPath($optional_path = null): string
    {
        if ($optional_path === 'laravel.log') {
            $optional_path = $this->fixLaravelLogFileName($optional_path);
        }

        $optional_path = $optional_path === null
            ? ''
            : DIRECTORY_SEPARATOR . ltrim($optional_path, '\\/');

        return rtrim($this->app->storagePath(), '\\/') . DIRECTORY_SEPARATOR . 'logs' . $optional_path;
    }

    /**
     * Get the log file content.
     *
     * @param string $file_name
     *
     * @return string|null
     */
    public function getLogFileContent(string $file_name = 'laravel.log'): ?string
    {
        $file_path = $this->getDefaultLogsDirectoryPath($file_name);

        $this->assertFileExists($file_path, "Log file [{$file_path}] does not exists.");

        if (!$content = \file_get_contents($file_path)) {
            return null;
        }
        return $content;
    }

    /**
     * Get log files last lines an an array of strings.
     *
     * @param string $file_name
     * @param int|null $lines_limit Lines limit from file end
     *
     * @return string[]
     */
    public function getLogFileContentAsArray(string $file_name = 'laravel.log', $lines_limit = null): array
    {
        $content = $this->getLogFileContent($file_name);
        $lines = \preg_split('/\\r|\\n/', $content);
        $lines_count = \count($lines);

        if ($lines_limit !== null && $lines_limit > 0 && $lines_count >= $lines_limit) {
            $lines = \array_splice($lines, $lines_count - $lines_limit);
        }

        return \array_values(\array_filter($lines));
    }
}
