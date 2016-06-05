<?php
namespace pocketmine\command\defaults;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\item\ItemBlock;
use pocketmine\item\Item;
use pocketmine\level\Level;

class FillCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.fill.description",
			"%commands.fill.usage"
		);
		$this->setPermission("pocketmine.command.fill");
	}

	public function execute(CommandSender $sender, $label, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		for($a = 0; $a < 6; $a++){
			if(isset($args[$a])){
				if(is_integer($args[$a])){
					if(Item::fromString($args[6]) instanceof ItemBlock){
						for($x = $args[0]; $x <= $args[3]; $x++){
							for($y = $args[1]; $y <= $args[4]; $y++){
								for($z = $args[2]; $z <= $args[5]; $z++){
									$this->setBlock(new Vector3($x, $y, $z), $sender->getLevel(), Item::fromString($args[6]), isset($args[7]) ? $args[7] : 0);
									$sender->sendMessage();
									return true;
								}
							}
						}
					}
					$sender->sendMessage(TextFormat::RED . new TranslationContainer("pocketmine.command.fill.invalidBlock", []));
					return false;
				}
				$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
				return false;
			}
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
			return false;
		}
	}

	private function setBlock(Vector3 $p, Level $lvl, ItemBlock $b,$meta = 0){
		$block = $b->getBlock();
		$block->setDamage($meta);
		$lvl->setBlock($p, $b);
		return true;
	}
}
