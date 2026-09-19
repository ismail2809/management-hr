<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SafeFileUpload implements ValidationRule
{
    private const BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar',
        'pl', 'py', 'sh', 'bash', 'cgi', 'exe', 'bat', 'cmd',
        'asp', 'aspx', 'jsp', 'js', 'mjs', 'vbs', 'ps1', 'svg',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $files = is_array($value) ? $value : [$value];

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $ext = strtolower($file->getClientOriginalExtension());

            if (in_array($ext, self::BLOCKED_EXTENSIONS, true)) {
                $fail("Le type de fichier .$ext n'est pas autorisé.");
                return;
            }
        }
    }
}
