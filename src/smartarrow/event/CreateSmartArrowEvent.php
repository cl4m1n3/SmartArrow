<?php

namespace smartarrow\event;

use pocketmine\event\{Cancellable, CancellableTrait};
use pocketmine\entity\projectile\Arrow;
use pocketmine\player\Player;

class CreateSmartArrowEvent extends SmartArrowEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Arrow $arrow,
        private Player $author,
        private Player $target
    ) {
        $this->arrow = $arrow;
    }

    public function getAuthor(): Player
    {
        return $this->author;
    }

    public function getTarget(): Player
    {
        return $this->target;
    }
}