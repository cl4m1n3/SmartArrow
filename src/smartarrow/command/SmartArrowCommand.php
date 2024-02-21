<?php

namespace smartarrow\command;

use pocketmine\command\{Command, CommandSender};
use pocketmine\Player;

use smartarrow\Loader;

class SmartArrowCommand extends Command
{
    const PERMISSION = "use.smartarrow";

    /** @var Loader */
    private $plugin;

    public function __construct(Loader $plugin, string $command_name, array $sub)
    {
        $this->plugin = $plugin;
        parent::__construct($command_name, "on/off smart arrow", "", $sub);
        $this->setPermission(self::PERMISSION);
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if (!($sender instanceof Player))
            return;

        if (!$sender->hasPermission(self::PERMISSION)) {
            $sender->sendMessage($this->plugin->getMessage("dont_have_permission"));
            return;
        }

        $this->plugin->updateStatus($sender);

        $sender->sendMessage([
            true => $this->plugin->getMessage("status_activated"),
            false => $this->plugin->getMessage("status_deactivated")
        ][$this->plugin->getStatus($sender)]);
    }
}