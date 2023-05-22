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
                    if($target = SmartArrow::getTarget($player))
                    {
                        $player->sendPopup(TextFormat::WHITE . "Target: " . TextFormat::GREEN . $target->getName());
                    }else{
                        $player->sendPopup(TextFormat::WHITE . "Target: " . TextFormat::RED . "NONE");
                    }
                    
                    // Changing the trajectory of the arrow
                    if($target = SmartArrow::getTarget($player))
                    {
                        foreach($player->getWorld()->getEntities() as $entity)
                        {
                            if($entity instanceof Arrow && $entity->getOwningEntity() == $player)
                            {
                                // Drawing up the vector of the arrow direction to the target
                                $arrow_loc = $entity->getLocation();
                                $target_loc = $target->getLocation();

                                $arrow_x = $arrow_loc->getX();
                                $arrow_y = $arrow_loc->getY();
                                $arrow_z = $arrow_loc->getZ();

                                $target_x = $target_loc->getX();
                                $target_y = $target_loc->getY() + 3;
                                $target_z = $target_loc->getZ();

                                $horizontal = sqrt(($target_x - $arrow_x) ** 2 + ($target_z - $arrow_z) ** 2);
                                $vertical = $target_y - ($arrow_y + 1);
                                $pitch = -atan2($vertical, $horizontal) / M_PI * 180; //negative is up, positive is down

                                $xDist = $target_x - $arrow_x;
                                $zDist = $target_z - $arrow_z;

                                $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
                                if($yaw < 0)
                                {
                                    $yaw += 360.0;
                                }
                                
                                $y = -sin(deg2rad($pitch));
                                $xz = cos(deg2rad($pitch));
                                $x = -$xz * sin(deg2rad($yaw));
                                $z = $xz * cos(deg2rad($yaw));

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