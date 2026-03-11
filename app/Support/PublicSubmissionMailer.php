<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class PublicSubmissionMailer
{
    public static function send(string $subject, string $view, array $data = [], array $files = []): void
    {
        Mail::send($view, $data, function (Message $message) use ($subject, $files): void {
            $message
                ->to(config('hirehelper.support_inbox'), config('hirehelper.support_name'))
                ->subject($subject);

            foreach (self::flattenFiles($files) as $file) {
                if (! $file instanceof UploadedFile || ! $file->isValid()) {
                    continue;
                }

                $message->attach($file->getRealPath(), [
                    'as' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType() ?: 'application/octet-stream',
                ]);
            }
        });
    }

    /**
     * @return array<int, UploadedFile>
     */
    public static function flattenFiles(array $files): array
    {
        $flattened = [];

        array_walk_recursive($files, function ($file) use (&$flattened): void {
            if ($file instanceof UploadedFile) {
                $flattened[] = $file;
            }
        });

        return $flattened;
    }
}
