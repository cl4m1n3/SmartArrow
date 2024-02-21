<?php
declare(strict_types=1);

namespace smartarrow;

use smartarrow\command\SmartArrowCommand;
use pocketmine\{Player, utils\Config, plugin\PluginBase};

class Loader extends PluginBase
{

    /** @var self */
    private static $instance;

    /** @var Config */
    private $messages, $settings;

    /**
     * $players - smart arrow statuses of players
     * $arrows - processing smart arrows
     */
    public $players = [], $arrows = [];

    public function onEnable()
    {
        self::$instance = $this;

        @mkdir($this->getDataFolder());

        $this->saveResource("messages.yml");
        $this->saveResource("settings.yml");
        $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        $this->settings = new Config($this->getDataFolder() . "settings.yml", Config::YAML);

        $this->getServer()->getPluginManager()->registerEvents(new PluginListener($this), $this);

        $command_name = (string) $this->settings->get("command.name");
        $this->getServer()->getCommandMap()->register(
            $command_name,
            new SmartArrowCommand($this, $command_name, $this->settings->get("command.sub"))
        );
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Task($this), 1);

        $this->fixSettingsValues();
    }

    private function fixSettingsValues()
    {
        $value = $this->settings->get("arrow.speed");
        if ($value < 1 or $value > 5) {
            $this->settings->set("arrow.speed", 2);
            $this->settings->save();
        }

        $value = $this->settings->get("capturing_target.max_distance");
        if ($value < 1 or $value > 250) {
            $this->settings->set("capturing_target.max_distance", 150);
            $this->settings->save();
        }

        $value = $this->settings->get("arrow.max_flight_time");
        if ($value < 1 or $value > 30) {
            $this->settings->set("arrow.max_flight_time", 10);
            $this->settings->save();
        }
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public function getStatus(Player $player): bool
    {
        $nick = strtolower($player->getName());
        return isset($this->players[$nick]) ? $this->players[$nick]["status"] : false;
    }

    public function updateStatus(Player $player)
    {
        $this->players[strtolower($player->getName())] = ["status" => !$this->getStatus($player)];
    }

    public function getMessage(string $name)
    {
        return $this->messages->get($name);
    }

    public function getSettingValue(string $name)
    {
        return $this->settings->get($name);
    }
}
