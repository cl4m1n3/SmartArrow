<?php
declare(strict_types=1);

namespace smartarrow\utils;

use pocketmine\math\Vector3;

class Math
{
    public static function getYaw(Vector3 $vector): float
    {
        $degrees = atan2($vector->getX(), $vector->getZ()) / M_PI * 180 - 90;
        return $degrees > 0 ? $degrees : $degrees + 360.0;
    }

    public static function getPitch(Vector3 $vector): float
    {
        return -atan2($vector->getY(), sqrt($vector->getX() ** 2 + $vector->getZ() ** 2)) / M_PI * 180;
    }

    public static function isOverloaded(Vector3 $vector1, Vector3 $vector2, float $corner): bool
    {
        $vector1_yaw = self::getYaw($vector1);
        $vector2_yaw = self::getYaw($vector2);

        $vector1_pitch = self::getPitch($vector1);
        $vector2_pitch = self::getPitch($vector2);

        if (abs($vector1_yaw - $vector2_yaw) > $corner or abs($vector1_pitch - $vector2_pitch) > $corner)
            return true;

        return false;
    }
}