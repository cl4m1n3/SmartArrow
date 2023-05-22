<?php

namespace cl4m1n3\smartarrow;

use pocketmine\scheduler\Task;
use pocketmine\entity\projectile\Arrow;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use cl4m1n3\smartarrow\SmartArrow;

class Tasks extends Task{
    
    private $main;
    
    public function __construct($main){
        $this->main = $main;
    }
    public function onRun() : void{
        foreach($this->main->getServer()->getOnlinePlayers() as $player)
        {
            if(SmartArrow::getStatus($player)) // If the status is true
            {
                if($player->getInventory()->getItemInHand()->getId() == 261)
                {
                    $tip = TextFormat::WHITE . "Target: " . TextFormat::RED . "NONE";
                    if($target = SmartArrow::getTarget($player))
                    {
                        $tip = TextFormat::WHITE . "Target: " . TextFormat::GREEN . $target->getName();
                    }
                    $player->sendTip($tip);
                    
                    // Changing the trajectory of the arrow
                    if($target = SmartArrow::getTarget($player))
                    {
                        foreach($player->getWorld()->getEntities() as $entity)
                        {
                            if($entity instanceof Arrow && $entity->getOwningEntity() == $player)
                            {
                                // Drawing up the vector of the arrow direction to the target
                                $aloc = $entity->getLocation(); // Arrow location
                                $tloc = $target->getLocation(); // Target location
                                
                                $x = $tloc->getX() - $aloc->getX();
                                $y = ($tloc->getY() + 2) - $aloc->getY();
                                $z = $tloc->getZ() - $aloc->getZ();
                                
                                if($aloc->distance($tloc) >= 15 && $aloc->getY() - $tloc->getY() > 0 && $aloc->getY() - $tloc->getY() <= 10)
                                {   
                                    $y = 0;
                                }
                                
                                $direction = (new Vector3($x, $y, $z))->normalize();
                                $entity->setMotion($direction->multiply(3));
                            }
                        }
                    }
                }
            }
        }
    }
}