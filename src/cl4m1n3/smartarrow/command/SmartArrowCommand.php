<?php

namespace cl4m1n3\smartarrow\command;

use cl4m1n3\smartarrow\SmartArrow;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\Server;

class SmartArrowCommand extends Command
{
    public function __construct()
    {
        $this->setPermission("use.smartarrow");
        parent::__construct("smartarrow", "smart arrow", "use /smartarrow", ["sarrow"]);
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if ($sender->hasPermission("use.smartarrow")) {

                if (count($args) > 0) {
                    if ($target = Server::getInstance()->getPlayerByPrefix($args[0])) {
                        if ($target !== $sender) {
                            SmartArrow::getInstance()->setTarget($sender, $target);
                            $sender->sendMessage(TextFormat::WHITE . "The target is set: " . TextFormat::GREEN . $target->getName());
                            return;
                        }
                        $sender->sendMessage(TextFormat::RED . "You can't specify yourself as a target!");
                        return;
                    }
                    $sender->sendMessage(TextFormat::RED . "This player is not online");
                    return;
                }

                $status = TextFormat::RED . "deactivated";
                SmartArrow::getInstance()->setStatus($sender);

                if (SmartArrow::getInstance()->getStatus($sender)) {
                    $status = TextFormat::GREEN . "activated";
                }

                $sender->sendMessage(TextFormat::WHITE . "The automatic arrow has been successfully " . $status . TextFormat::WHITE . "!");
                return;
            }
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command!");
        }
    }
}