<?php
declare(strict_types=1);

namespace smartarrow\utils;

use pocketmine\math\Vector3;
use pockemtine\block\Air;
use pocketmine\world\World;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

class Physics extends Math
{
    public static function objectIsFieldOfView(Entity|Player $entity, Entity|Player $object, int $view): bool
    {
        $entity_pos = $entity->getPosition();
        $object_pos = $entity->getPosition();

        // VERTICAL
        $direction = $object_pos->subtractVector($entity_pos)->normalize();
        $direction_yaw = self::getYaw($direction);
        $direction_pitch = self::getPitch($direction);

        // HORIZONTAL
        $direction = $entity->getDirectionVector()->normalize();
        $entity_yaw = self::getYaw($direction);
        $entity_pitch = self::getPitch($direction);

        if (abs($direction_yaw - $entity_yaw) <= $view or abs($direction_pitch - $entity_pitch) <= $view)
            return true;

        return false;
    }
}