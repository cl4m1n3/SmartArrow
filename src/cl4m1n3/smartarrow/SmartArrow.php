<?php
declare(strict_types=1);

namespace cl4m1n3\smartarrow;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\Server;
use cl4m1n3\smartarrow\command\SmartArrowCommand;
use cl4m1n3\smartarrow\event\Event;

class SmartArrow extends PluginBase
{
    private static array $players = [];
    private static array $targets = [];
    
    protected function onEnable() : void
    {
        Server::getInstance()->getPluginManager()->registerEvents(new Event(), $this);
        Server::getInstance()->getCommandMap()->register("smartarrow", new SmartArrowCommand());
        $this->getScheduler()->scheduleRepeatingTask(new Tasks($this), 5);
    }
    
    public static function getStatus(Player $player) : bool
    {
        $nick = $player->getName();
        return array_key_exists($nick, self::$players) ? self::$players[$nick] : false;
    }
    
    public static function getTarget(Player $player) : ?Player
    {
        $nick = $player->getName();
        if(array_key_exists($player->getName(), self::$targets))
        {
            $target = Server::getInstance()->getPlayerByPrefix(self::$targets[$nick]);
            return $target ? $target : null;
        }
        return null;
    }
    
    public static function setStatus(Player $player, bool $status) : void
    {
        self::$players[$player->getName()] = $status;
    }
    
    public static function setTarget(Player $player, $target) : void
    {
        self::$targets[$player->getName()] = $target->getName();
    }
}