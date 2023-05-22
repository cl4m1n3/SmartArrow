<?php

namespace cl4m1n3\smartarrow\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use cl4m1n3\smartarrow\SmartArrow;

class SmartArrowCommand extends Command
{
    public function __construct()
    {
        parent::__construct("smartarrow", "smart arrow", "use /smartarrow");
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player)
        {
            if($sender->hasPermission("use.smartarrow"))
            {
                if(SmartArrow::getStatus($sender)) // If the status is true
                {
                    SmartArrow::setStatus($sender, false);
                    $sender->sendMessage(TextFormat::WHITE . "The automatic arrow has been successfully " . TextFormat::RED . "deactivated" . TextFormat::WHITE . "!");
                    return;
                }
                SmartArrow::setStatus($sender, true);
                $sender->sendMessage(TextFormat::WHITE . "The automatic arrow has been successfully " . TextFormat::GREEN . "activated" . TextFormat::WHITE . "!");
                return;
            }
            $sender->sendMessage(TextFormat::RED . "§cYou do not have permission to use this command!");
        }
    }
}