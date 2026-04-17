<?php

namespace App\Support;

class MessageFormatter
{
    /**
     * Convert URLs in text to clickable links.
     */
    public static function linkify(string $text, bool $light = false): string
    {
        $pattern = '/(https?:\/\/[^\s<>"\']+)/i';

        $style = $light
            ? 'color:#c7caff;text-decoration:underline'
            : 'color:#4b4ff5;text-decoration:underline';

        return preg_replace(
            $pattern,
            '<a href="$1" target="_blank" rel="noopener noreferrer" style="' . $style . ';word-break:break-all">$1</a>',
            $text
        );
    }
}
