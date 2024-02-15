<?php
declare(strict_types=1);

namespace smartarrow\utils;

use pocketmine\entity\Entity;
use pocketmine\player\Player;

class Physics extends Math
{
    public static function objectIsFieldOfView(Player $entity, Player $object, int $view): bool
    {
        $entity_pos = $entity->getPosition();
        $object_pos = $object->getPosition();

        $direction1 = $object_pos->subtractVector($entity_pos)->normalize();
        $direction2 = $entity->getDirectionVector()->normalize();

        return !self::isOverloaded($direction1, $direction2, $view);
    }

}