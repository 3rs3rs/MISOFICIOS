<?php

namespace App\Services;

class WhatsAppService
{
    public const COUNTRY_CODE = '52';

    public function waLink(string $phone, string $message): string
    {
        $phone = $this->normalizePhone($phone);

        if ($phone === '') {
            return '';
        }

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (! str_starts_with($digits, self::COUNTRY_CODE)) {
            $digits = self::COUNTRY_CODE . $digits;
        }

        return $digits;
    }
}
