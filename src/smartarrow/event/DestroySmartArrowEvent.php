<?php

namespace smartarrow\event;

use pocketmine\event\{Cancellable, CancellableTrait};
use pocketmine\entity\projectile\Arrow;

class DestroySmartArrowEvent extends SmartArrowEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Arrow $arrow
    ) {
        $this->arrow = $arrow;
    }
}