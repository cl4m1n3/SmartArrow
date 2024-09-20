<?php

namespace smartarrow\event;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\entity\projectile\Arrow;

abstract class SmartArrowEvent extends PluginEvent
{
    public static $handlerList;

    protected Arrow $arrow;

    public function getArrow(): Arrow
    {
        return $this->arrow;
    }
}