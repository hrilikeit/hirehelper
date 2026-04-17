<?php

namespace App\Support;

class MessageFormatter
{
    /**
     * Convert URLs in text to clickable links.
     */
    public static function linkify(string $text): string
    {
        $pattern = '/(https?:\/\/[^\s<>"\']+)/i';

        return preg_replace(
            $pattern,
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="msg-link">$1</a>',
            $text
        );
    }
}
