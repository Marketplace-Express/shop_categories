<?php
/**
 * User: Wajdi Jurry
 * Date: 01/03/19
 * Time: 03:55 م
 */

namespace Shop_categories\Enums;


class LanguagesEnum
{
    const LANG_ENGLISH = 'en';
    const LANG_FRENCH = 'fr';

    /**
     * @return array
     */
    public static function getLanguages(): array
    {
        return [
            self::LANG_ENGLISH,
            self::LANG_FRENCH
        ];
    }
}