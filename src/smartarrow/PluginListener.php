<?php
declare(strict_types=1);

namespace smartarrow;

use pocketmine\event\{Listener, entity\EntityShootBowEvent, entity\ProjectileHitEvent};
use pocketmine\player\Player;
use smartarrow\utils\Physics;

class PluginListener implements Listener
{
    /** @var Loader */
    private $plugin;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onShoot(EntityShootBowEvent $event)
    {
        $entity = $event->getEntity();
        $projectile = $event->getProjectile();

        if (!($entity instanceof Player))
            return;

        if (!$this->plugin->getStatus($entity))
            return;

        if ($object = $this->getNearestPlayerFromPlayer($entity)) {
            $this->plugin->arrows[$projectile->getId()] = [
                "target" => $object,
                "last_motion" => $entity->getDirectionVector()->normalize(),
                "time" => time()
            ];
            $entity->sendPopup(str_replace("{NICK}", $object->getName(), $this->plugin->getMessage("target_is_set")));
            return;
        }

        $entity->sendPopup($this->plugin->getMessage("no_targets_found"));
    }

    public function onHit(ProjectileHitEvent $event)
    {
        $projectile = $event->getEntity();
        $id = $projectile->getId();

        if (!isset($this->plugin->arrows[$id]))
            return;

        unset($this->plugin->arrows[$id]);
    }

    public function getNearestPlayerFromPlayer(Player $player): ?Player
    {
        $result = null;

        foreach ($player->getWorld()->getPlayers() as $object) {
            if ($object == $player)
                continue;

            $entity_pos = $object->getPosition()->add(0, 1.5, 0);
            $player_pos = $player->getPosition()->add(0, $player->getEyeHeight(), 0);
            $distance = $entity_pos->distance($player_pos);

            if ($distance > (float) $this->plugin->getSettingValue("capturing_target.max_distance"))
                continue;

            if (!Physics::objectIsFieldOfView($player, $object, 45))
                continue;

            if (is_null($result)) {
                $result = $object;
                continue;
            }

            if ($distance < $result->getPosition()->distance($object->getPosition())) {
                $result = $object;
            }
        }

        return $result;
    }
}