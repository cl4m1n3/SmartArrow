<?php
declare(strict_types=1);

namespace smartarrow;

use pocketmine\{utils\Config, plugin\PluginBase};
use smartarrow\command\SmartArrowCommand;

class Loader extends PluginBase
{

    /** @var self */
    private static $instance;

    /** @var Config */
    private $messages, $settings;

    /** @var Manager */
    public $manager;

    protected function onEnable(): void
    {
        self::$instance = $this;

        $this->saveResource("messages.yml");
        $this->saveResource("settings.yml");
        $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        $this->settings = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
        $this->fixSettingsValues();

        if ($this->settings->get("command.status")) {
            $command_name = (string) $this->settings->get("command.name");
            $this->getServer()->getCommandMap()->register(
                $command_name,
                new SmartArrowCommand($this, $command_name, $this->settings->get("command.sub"))
            );
        }

        $this->getServer()->getPluginManager()->registerEvents(new PluginListener($this), $this);
        $this->getScheduler()->scheduleRepeatingTask(new PluginTask($this), 1);

        $this->manager = new Manager($this);
    }

    private function fixSettingsValues(): void
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

    public function getManager(): Manager
    {
        return $this->manager;
    }

    public function getMessage(string $name): string|bool
    {
        return $this->messages->get($name);
    }

    public function getSettingValue(string $name): mixed
    {
        return $this->settings->get($name);
    }
}
