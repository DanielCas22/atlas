<?php

/**
 * Flash message helper for displaying temporary messages
 */
class FlashMessage
{
    /**
     * Set a flash message
     * @param string $type Message type (success, error, warning, info)
     * @param string $message The message content
     */
    public static function set($type, $message)
    {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get and clear the flash message
     * @return array|null The flash message or null if none
     */
    public static function get()
    {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        return null;
    }

    /**
     * Display flash message in HTML
     * @return string HTML for the flash message
     */
    public static function display()
    {
        $message = self::get();
        if (!$message) {
            return '';
        }

        $alertClass = 'alert-' . ($message['type'] === 'error' ? 'danger' : $message['type']);
        return '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">
            <i class="bi bi-' . self::getIcon($message['type']) . ' me-2"></i>
            ' . htmlspecialchars($message['message']) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
    }

    /**
     * Get Bootstrap icon for message type
     * @param string $type Message type
     * @return string Icon name
     */
    private static function getIcon($type)
    {
        $icons = [
            'success' => 'check-circle',
            'error' => 'exclamation-triangle',
            'warning' => 'exclamation-circle',
            'info' => 'info-circle'
        ];
        return $icons[$type] ?? 'info-circle';
    }
}