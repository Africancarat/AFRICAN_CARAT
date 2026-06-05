<?php

namespace App\Helpers;

final class CertificationHelper
{
    /**
     * Prefer an explicit storefront URL when present (vendor / lab deeplink saved on the stone).
     */
    public static function certificateLink(?string $storedUrl, ?string $lab, ?string $certificateNumber): ?string
    {
        if (filled(trim((string) $storedUrl ?? ''))) {
            return $storedUrl;
        }

        return self::guessLaboratoryVerificationUrl($lab, $certificateNumber);
    }

    public static function guessLaboratoryVerificationUrl(?string $lab, ?string $certificateNumber): ?string
    {
        $cert = preg_replace('/\s+/', '', (string) $certificateNumber);
        if ($cert === '') {
            return null;
        }

        $l = strtolower((string) $lab);

        if (str_contains($l, 'gia')) {
            return 'https://www.gia.edu/report-check?reportno=' . rawurlencode($cert);
        }
        if (str_contains($l, 'igi')) {
            return 'https://www.igi.org/report-check?r=' . rawurlencode($cert);
        }
        if (str_contains($l, 'hrd')) {
            return 'https://my.hrdantwerp.com/?record_number=' . rawurlencode($cert);
        }
        if (str_contains($l, 'ags') || str_contains($l, 'agl')) {
            return 'https://www.agslabs.com/report-information.html?report_no=' . rawurlencode($cert);
        }

        return null;
    }

    /** @return non-falsy-string */
    public static function storageImageUrl(?string $path): string
    {
        return ImageHelper::storageImageUrl($path, '');
    }

}
