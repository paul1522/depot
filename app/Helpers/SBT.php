<?php

namespace App\Helpers;

use Carbon\Carbon;

class SBT
{
    public static function itemPrefix(string $item): string
    {
        return mb_ereg_replace('(-[A-Z])?$', '', $item);
    }

    public static function itemSuffix(string $item): string
    {
        $a = mb_ereg('(-([A-Z]))?$', $item, $matches);
        if (! $a || ! $matches[2]) {
            return '';
        }

        return $matches[2];
    }

    public static function date(string $Ymd): Carbon
    {
        return Carbon::createFromFormat('Ymd', $Ymd);
    }
}
