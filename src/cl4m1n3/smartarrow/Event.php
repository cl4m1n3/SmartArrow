<?php

namespace cl4m1n3\smartarrow;

use cl4m1n3\smartarrow\SmartArrow;
use pocketmine\entity\Entity;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent, EntityShootBowEvent};
use pocketmine\event\Listener;
use pocketmine\item\Bow;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Event implements Listener
{
    public function onDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();

        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();

            if ($damager instanceof Player && $entity instanceof Entity && $damager !== $entity) {
                if (SmartArrow::getInstance()->getStatus($damager) && $damager->getInventory()->getItemInHand() instanceof Bow) {
                    SmartArrow::getInstance()->setTarget($damager, $entity);
                }
            }
        }
    }

    public function onShoot(EntityShootBowEvent $event): void
    {
        $entity = $event->getEntity();
        if (SmartArrow::getInstance()->getStatus($entity)) {
            if ($target = SmartArrow::getInstance()->getTarget($entity)) {
                if ($entity->getPosition()->distance($target->getPosition()) >= 250) {
                    $entity->sendMessage(TextFormat::GOLD . "The arrow cannot be aimed at the target, since the distance between the arrow and the target is more than 250 blocks!");
                }
            }
        }
    }
}