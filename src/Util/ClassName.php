<?php
declare(strict_types=1);

namespace Siebels\Pedigree\Util;

final class ClassName
{
    public static function normalize(string $fqcn): string
    {
        $ret = str_replace('_', '__', $fqcn);
        $ret = str_replace('\\', '_', $fqcn);

        return $ret;
    }
}