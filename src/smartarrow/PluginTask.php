<?php

namespace smartarrow;

use pocketmine\scheduler\Task;
use smartarrow\{Loader, utils\Physics};

class PluginTask extends Task
{
    /** @var Loader */
    private $plugin;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun(): void
    {
        foreach ($this->plugin->arrows as $entity_id => $data) {
            if ($arrow = $this->plugin->getServer()->getWorldManager()->findEntity($entity_id)) {

                $speed = $this->plugin->getSettingValue("arrow.speed");

                if (is_null($data["target"])) {
                    $arrow->setMotion($data["last_motion"]->multiply($speed));
                    continue;
                }

                $target_pos = $data["target"]->getPosition()->add(0, 1.5, 0);
                $arrow_pos = $arrow->getPosition();
                $vector = $target_pos->subtractVector($arrow_pos)->normalize();
                $motion = $data["last_motion"]->addVector($vector)->normalize();

                if (Physics::isOverloaded($data["last_motion"], $motion, 45)) {
                    $arrow->setMotion($data["last_motion"]->multiply($speed));
                    continue;
                }

                $arrow->setMotion($motion->multiply($speed));
                $this->plugin->arrows[$entity_id]["last_motion"] = $motion;

                if (($data["time"] + $this->plugin->getSettingValue("arrow.max_flight_time")) - time() <= 0) {
                    unset($this->plugin->arrows[$entity_id]);
                    continue;
                }
            }
        }
    }
}