<?php

namespace smartarrow;

use pocketmine\scheduler\PluginTask;
use smartarrow\{Loader, utils\Physics};

class Task extends PluginTask
{
    /** @var Loader */
    private $plugin;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun($currentTicks)
    {
        foreach ($this->plugin->arrows as $entity_id => $data) {
            if ($arrow = $this->plugin->getServer()->findEntity($entity_id)) {

                $speed = $this->plugin->getSettingValue("arrow.speed");

                if (is_null($data["target"])) {
                    $arrow->setMotion($data["last_motion"]->multiply($speed));
                    continue;
                }

                $arrow_pos = $arrow->getPosition();
                $target_pos = $data["target"]->getPosition();

                if (!$data["target"]->isGliding())
                    $target_pos = $target_pos->add(0, 1.5, 0);

                $preemptive = $target_pos->subtract($data["target_last_position"]);
                $target_speed = Physics::getSpeed($target_pos, $data["target_last_position"], 1);

                $projectile_speed = Physics::getSpeed($data["last_position"], $arrow_pos, 1);
                $projectile_speed = $projectile_speed > 0 ? $projectile_speed : 1;

                $projectile_flight_time = $arrow_pos->distance($target_pos) / $projectile_speed;
                $preemptive = Physics::getPreemptive($projectile_flight_time, $target_speed, $preemptive);

                $this->plugin->arrows[$entity_id]["last_position"] = $arrow_pos;
                $this->plugin->arrows[$entity_id]["target_last_position"] = $target_pos;

                $vector = $target_pos->add($preemptive)->subtract($arrow_pos)->normalize();
                $motion = $data["last_motion"]->add($vector)->normalize();

                if (!Physics::isOverloaded($data["last_motion"], $motion, 45)) {
                    $arrow->setMotion($motion->multiply($speed));
                    $this->plugin->arrows[$entity_id]["last_motion"] = $motion;
                    $this->plugin->arrows[$entity_id]["last_position"] = $arrow_pos;
                } else {
                    $arrow->setMotion($data["last_motion"]->multiply($speed));
                }

                if (($data["time"] + $this->plugin->getSettingValue("arrow.max_flight_time")) - time() <= 0)
                    unset($this->plugin->arrows[$entity_id]);
            }
        }
    }
}
