<?php
declare(strict_types=1);

namespace smartarrow;

use pocketmine\player\{Player, Gamemode};
use pocketmine\entity\projectile\Arrow;
use smartarrow\utils\Physics;
use smartarrow\event\{CreateSmartArrowEvent, DestroySmartArrowEvent, SmartArrowMoveEvent};

class Manager
{
    /** If this argument is changed, the accuracy of hitting players who move at high speed drops by 70%+ */
    public const SPEED_MULTIPLIER = 2.5;

    /** @var Loader */
    private $plugin;

    /** @var array $players Player[]*/
    public $players = [];

    /**
     * An array for processing the flight of arrows. 
     * @var array
     */
    public $arrows = [];

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function registerPlayer(Player $player): void
    {
        $this->players[] = $player;
    }

    public function hasPlayer(Player $player): bool
    {
        return in_array($player, $this->players);
    }

    public function removePlayer(Player $player): void
    {
        if (!$this->hasPlayer($player))
            return;

        unset($this->players[array_search($player, $this->players)]);
    }

    public function registerArrow(Arrow $arrow, Player $author, Player $target): void
    {
        $ev = new CreateSmartArrowEvent($arrow, $author, $target);
        $ev->call();

        if ($ev->isCancelled())
            return;

        $this->plugin->getManager()->arrows[$arrow->getId()] = [
            "target" => $target,
            "target_last_position" => $target->getEyePos(),
            "last_position" => $arrow->getPosition(),
            "last_motion" => $author->getDirectionVector()->normalize(),
            "time" => time()
        ];
    }

    /**
     * Returns true if the arrow was successfully destroyed and false if it did not happen.
     */
    public function destroyArrow(Arrow $arrow): bool
    {
        $ev = new DestroySmartArrowEvent($arrow);
        $ev->call();

        if ($ev->isCancelled())
            return false;

        if (!isset($this->arrows[$arrow->getId()]))
            return false;

        unset($this->arrows[$arrow->getId()]);
        return true;
    }

    public function onArrow(Arrow $arrow): void
    {
        $arrow_pos = $arrow->getPosition();
        $data = $this->arrows[($entity_id = $arrow->getId())];

        if (is_null($data["target"]) or $data["target"]->getGamemode() == Gamemode::SPECTATOR()) {
            $arrow->setMotion($data["last_motion"]->multiply(self::SPEED_MULTIPLIER));
            $this->arrows[$entity_id]["last_position"] = $arrow_pos;
            return;
        }

        $target_pos = $data["target"]->getEyePos();
        $preemptive = $data["target"]->getDirectionVector();

        $target_speed = round(Physics::getSpeed($target_pos, $data["target_last_position"], 1), 2);
        $projectile_speed = round(Physics::getSpeed($data["last_position"], $arrow_pos, 1), 2);
        $projectile_speed = $projectile_speed > 0 ? $projectile_speed : 1;

        $projectile_flight_time = $arrow_pos->distance($target_pos) / $projectile_speed;
        $preemptive = Physics::getPreemptive($projectile_flight_time, $target_speed, $preemptive);

        $this->arrows[$entity_id]["last_position"] = $arrow_pos;
        $this->arrows[$entity_id]["target_last_position"] = $target_pos;

        $motion = $target_pos->addVector($preemptive)->subtractVector($arrow_pos)->normalize();

        $ev = new SmartArrowMoveEvent(
            $arrow,
            $motion,
            $preemptive,
            $overloaded = Physics::isOverloaded($data["last_motion"], $motion, 45),
            $target_speed,
            $projectile_speed
        );
        $ev->call();

        if (!$overloaded and !$ev->isCancelled()) {
            $arrow->setMotion($motion->multiply(self::SPEED_MULTIPLIER));
            $this->arrows[$entity_id]["last_motion"] = $motion;
            $this->arrows[$entity_id]["last_position"] = $arrow_pos;
        } else {
            $arrow->setMotion($data["last_motion"]->multiply(self::SPEED_MULTIPLIER));
        }

        if (($data["time"] + $this->plugin->getSettingValue("arrow.max_flight_time")) - time() <= 0)
            unset($this->arrows[$entity_id]);
    }
}
