<?php
declare(strict_types=1);

namespace smartarrow\utils;

use pocketmine\player\Player;
use pocketmine\math\Vector3;

class Physics extends Math
{
    public static function objectIsFieldOfView(Player $entity, Player $object, int|float $view): bool
    {
        $entity_pos = $entity->getPosition();
        $object_pos = $object->getPosition();

        $direction1 = $object_pos->subtractVector($entity_pos)->normalize();
        $direction2 = $entity->getDirectionVector()->normalize();

        return !self::isOverloaded($direction1, $direction2, $view);
    }

    /**
     * $s1 - start point
     * $s2 - end point
     * $time in ticks
     */
    public static function getSpeed(Vector3 $s1, Vector3 $s2, int|float $time): float
    {
        return $s1->distance($s2) / $time;
    }

    public static function getPreemptive(int|float $projeсtile_flight_time, int|float $target_speed, Vector3 $motion): Vector3
    {
        return $motion->multiply($target_speed * $projeсtile_flight_time);
    }
}
