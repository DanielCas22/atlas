<?php

/**
 * Logger class for system auditing and debugging
 * Provides simple file-based logging functionality
 */
class Logger
{
    private static $logFile = __DIR__ . '/../logs/system.log';
    private static $errorLogFile = __DIR__ . '/../logs/errors.log';

    /**
     * Log an informational message
     * @param string $message The message to log
     * @param array $context Additional context data
     */
    public static function info($message, $context = [])
    {
        self::log('INFO', $message, $context);
    }

    /**
     * Log a warning message
     * @param string $message The message to log
     * @param array $context Additional context data
     */
    public static function warning($message, $context = [])
    {
        self::log('WARNING', $message, $context);
    }

    /**
     * Log an error message
     * @param string $message The message to log
     * @param array $context Additional context data
     */
    public static function error($message, $context = [])
    {
        self::log('ERROR', $message, $context, self::$errorLogFile);
    }

    /**
     * Log an action performed by a user
     * @param string $action The action performed
     * @param string $user Username or identifier
     * @param array $details Additional details
     */
    public static function audit($action, $user = 'system', $details = [])
    {
        $message = "User '$user' performed action: $action";
        self::log('AUDIT', $message, $details);
    }

    /**
     * Generic logging method
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Context data
     * @param string $file Log file path
     */
    private static function log($level, $message, $context = [], $file = null)
    {
        $file = $file ?: self::$logFile;
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';

        $logEntry = "[$timestamp] [$level] $message{$contextStr}" . PHP_EOL;

        // Ensure log directory exists
        $logDir = dirname($file);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents($file, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Get recent log entries
     * @param int $lines Number of lines to retrieve
     * @return array Array of log entries
     */
    public static function getRecentLogs($lines = 50)
    {
        if (!file_exists(self::$logFile)) {
            return [];
        }

        $logs = file(self::$logFile);
        return array_slice(array_reverse($logs), 0, $lines);
    }
}