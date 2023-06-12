<?php

namespace cl4m1n3\smartarrow;

use cl4m1n3\smartarrow\SmartArrow;
use pocketmine\entity\projectile\Arrow;
use pocketmine\item\Bow;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class Tasks extends Task
{
    private $main;

    public function __construct($main)
    {
        $this->main = $main;
    }

    public function onRun(): void
    {
        foreach ($this->main->getServer()->getOnlinePlayers() as $player) {
            if (SmartArrow::getInstance()->getStatus($player)) {
                if ($player->getInventory()->getItemInHand() instanceof Bow) {
                    $text = TextFormat::RED . "NONE";

                    if ($target = SmartArrow::getInstance()->getTarget($player)) {
                        $distance = round($player->getPosition()->distance($target->getPosition()));
                        $text = ($distance <= 250) ? TextFormat::GREEN . $target->getName() . TextFormat::WHITE . " (" . $distance . " block's)" : TextFormat::GREEN . $target->getName() . TextFormat::WHITE . " (" . TextFormat::RED . $distance . TextFormat::WHITE . " block's)";
                    }

                    $player->sendTip(TextFormat::WHITE . "Target: " . $text);
                }

                // Changing the trajectory of the arrow
                if ($target = SmartArrow::getInstance()->getTarget($player)) {
                    foreach ($player->getWorld()->getEntities() as $entity) {
                        if ($entity instanceof Arrow && $entity->getOwningEntity() == $player) {

                            // Drawing up the vector of the arrow direction to the target
                            $aloc = $entity->getLocation(); // Arrow location
                            $tloc = $target->getLocation(); // Target location

                            $x = $tloc->getX() - $aloc->getX();
                            $y = ($tloc->getY() + 2) - $aloc->getY();
                            $z = $tloc->getZ() - $aloc->getZ();

                            if ($aloc->distance($tloc) >= 15 && $aloc->getY() - $tloc->getY() > 0 && $aloc->getY() - $tloc->getY() <= 25) {
                                $y = 2;
                            }

                            $direction = (new Vector3($x, $y, $z))->normalize();

                            if ($player->getPosition()->distance($aloc) >= 10 && $aloc->distance($tloc) <= 250) {
                                $entity->setMotion($direction->multiply(3));
                            }
                        }
                    }
                }
            }
        }
    }
}