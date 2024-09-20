<?php

namespace smartarrow\event;

use pocketmine\event\{Cancellable, CancellableTrait};
use pocketmine\entity\projectile\Arrow;
use pocketmine\math\Vector3;

class SmartArrowMoveEvent extends SmartArrowEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Arrow $arrow,
        private Vector3 $motion,
        private Vector3 $preemptive,
        private bool $is_overloaded,
        private int|float $target_speed,
        private int|float $arrow_speed,
    ) {
        $this->arrow = $arrow;
    }

    public function getMotion(): Vector3
    {
        return $this->motion;
    }

    public function getPreemptive(): Vector3
    {
        return $this->preemptive;
    }

    public function isOverloaded(): bool
    {
        return $this->is_overloaded;
    }

    public function getTargetSpeed(): int|float
    {
        return $this->target_speed;
    }

    public function getArrowSpeed(): int|float
    {
        return $this->arrow_speed;
    }
}