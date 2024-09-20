<?php

namespace smartarrow;

use pocketmine\scheduler\Task;

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
        foreach ($this->plugin->getManager()->arrows as $entity_id => $data) {
            if (!($arrow = $this->plugin->getServer()->getWorldManager()->findEntity($entity_id)))
                continue;

            $this->plugin->getManager()->onArrow($arrow);
        }
    }
}
