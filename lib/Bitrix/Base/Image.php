<?php

namespace bfday\PHPDailyFunctions\Bitrix\Base;

class Image
{
    protected static $staticDataInitiated = false;

    protected static $resizeTypes;

    /**
     * Call me baby.
     *
     * @return bool
     */
    protected static function init()
    {
        if (static::$staticDataInitiated) {
            return true;
        } else {
            static::$staticDataInitiated = true;
        }
        static::$resizeTypes = [
            BX_RESIZE_IMAGE_PROPORTIONAL,
            BX_RESIZE_IMAGE_EXACT,
            BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
        ];
        return true;
    }

    public static function getResized(
        $fileArray,
        $width,
        $height,
        $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL,
        $quality = false,
        $isReturnSizes = false,
        $filters = false,
        $isImmediate = false
    )
    {
        static::init();
        $width = intval($width);
        $height = intval($height);
        if ($width == 0 || $height == 0) {
            throw new \Exception('params {width} and {height} should be more 0');
        }
        if ($quality !== false) {
            $quality = intval($quality);
            if ($quality <= 0 || $quality > 100)
                throw new \Exception('Params {quality} must be more 0 and not greater than 100.');
        }
        if (!in_array($resizeType, static::$resizeTypes)) {
            throw new \Exception('Possible values for {$resizeType} can be: ' . print_r(static::$resizeTypes, true));
        }
        // ToDo check $fileArray here
        $size = [
            'width' => $width,
            'height' => $height,
        ];
        $res = \CFile::ResizeImageGet(
                $fileArray,
                $size,
                $resizeType,
                $isReturnSizes,
                $filters,
                $isImmediate,
                $quality
        );
        if ($res === false) return false;
        return $res;
    }

    public static function getResizedByFileID($fileID, $width, $height, $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL)
    {
        $arFile = File::getByID($fileID);
        if ($arFile === false) return false;
        return static::getResized(
            $arFile,
            $width,
            $height,
            $resizeType
        );
    }

    /**
     * ToDo: not implemented well. Should be based on http://dev.1c-bitrix.ru/api_help/main/reference/cfile/resizeimagefile.php
     *
     * @param $sourceFile
     * @param $destinationFile
     * @param $width
     * @param $height
     * @param int $resizeType
     * @throws \Exception
     */
    public static function getResizeFile($sourceFile, $destinationFile, $width, $height, $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL)
    {
        static::init();
        throw new \Exception('Not implemented yet');
        $width = intval($width);
        $height = intval($height);
        if ($width == 0 || $height == 0) {
            throw new \Exception('params [width] and [height] should be more 0');
        }
        if (!in_array($resizeType, static::$resizeTypes)) {
            throw new \Exception('Possible values for {$resizeType} can be: ' . print_r(static::$resizeTypes, true));
        }
        // ToDo check $fileArray here
    }
}