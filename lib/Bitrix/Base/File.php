<?php

namespace bfday\PHPDailyFunctions\Bitrix\Base;

class File
{
    /**
     * Returns file array or false.
     *
     * @param $fileId
     * @param bool|string $uploadDir
     * @return array|bool
     */
    public static function getFileArray($fileId, $uploadDir = false)
    {
        $fileId = intval($fileId);
        if ($fileId == 0) {
            return false;
        } else {
            return \CFile::GetFileArray($fileId, $uploadDir);
        }
    }

    public static function getByID($fileId)
    {
        $fileId = intval($fileId);
        if ($fileId == 0) {
            return false;
        } else {
            return static::getFileArray($fileId);
        }
    }
}