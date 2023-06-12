<?php
declare(strict_types=1);

namespace cl4m1n3\smartarrow;

use cl4m1n3\smartarrow\command\SmartArrowCommand;
use cl4m1n3\smartarrow\Event;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class SmartArrow extends PluginBase
{

    private static $instance;

    private array $players = [];

    private array $targets = [];

    public static function getInstance(): SmartArrow
    {
        return self::$instance;
    }

    protected function onEnable(): void
    {
        self::$instance = $this;

        Server::getInstance()->getPluginManager()->registerEvents(new Event(), $this);
        Server::getInstance()->getCommandMap()->register("smartarrow", new SmartArrowCommand());
        $this->getScheduler()->scheduleRepeatingTask(new Tasks($this), 5);
    }

    public function getStatus(Player $player): bool
    {
        $nick = $player->getName();
        return array_key_exists($nick, $this->players) ? $this->players[$nick] : false;
    }

    public function getTarget(Player $player): ?Entity
    {
        $nick = $player->getName();

        if (array_key_exists($player->getName(), $this->targets)) {
            return Server::getInstance()->getWorldManager()->findEntity($this->targets[$nick]);
        }
        return null;
    }

    public function setStatus(Player $player): void
    {
        $nick = $player->getName();
        $this->players[$nick] = !$this->getStatus($player);
    }

    public function setTarget(Player $player, Entity $target): void
    {
        $this->targets[$player->getName()] = $target->getId();
    }
}