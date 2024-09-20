<?php
declare(strict_types=1);

namespace smartarrow;

use pocketmine\event\{Listener, entity\EntityShootBowEvent, entity\ProjectileHitEvent};
use pocketmine\player\{Player, Gamemode};
use pocketmine\entity\projectile\Arrow;
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

        if (!($entity instanceof Player) or !($projectile instanceof Arrow))
            return;

        if (!$this->plugin->getManager()->hasPlayer($entity))
            return;

        if (!($target = $this->getNearestPlayerFromPlayer($entity))) {
            $event->cancel();
            $entity->sendMessage($this->plugin->getMessage("no_targets_found"));
            return;
        }

        $this->plugin->getManager()->registerArrow($projectile, $entity, $target);
    }

    public function onHit(ProjectileHitEvent $event)
    {
        $this->plugin->getManager()->destroyArrow($event->getEntity());
    }

    public function getNearestPlayerFromPlayer(Player $player): ?Player
    {
        $result = null;

        foreach ($player->getWorld()->getPlayers() as $object) {
            if ($object == $player)
                continue;

            if ($object->getGamemode() == Gamemode::SPECTATOR())
                continue;

            $entity_pos = $object->getEyePos();
            $player_pos = $player->getEyePos();
            $distance = $entity_pos->distance($player_pos);

            if ($distance > $this->plugin->getSettingValue("capturing_target.max_distance"))
                continue;

            if (!Physics::objectIsFieldOfView($player, $object, 30))
                continue;

            if (is_null($result)) {
                $result = $object;
                continue;
            }

            if ($distance < $result->getPosition()->distance($player_pos)) {
                $result = $object;
            }
        }

        return $result;
    }
}
