<?php

namespace cl4m1n3\smartarrow\event;

use pocketmine\event\Listener;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};
use pocketmine\player\Player;
use pocketmine\Server;
use cl4m1n3\smartarrow\SmartArrow;

class Event implements Listener
{
    
    private static $players = [];
    
    public function onDamage(EntityDamageEvent $event) : void
    {
        $entity = $event->getEntity();
        if($event instanceof EntityDamageByEntityEvent)
        {
            $damager = $event->getDamager();
            if($damager instanceof Player && $entity instanceof Player)
            {
                if(SmartArrow::getStatus($damager) && $damager->getInventory()->getItemInHand()->getId() == 261)
                {
                    SmartArrow::setTarget($damager, $entity);
                }
            }
        }
    }
}