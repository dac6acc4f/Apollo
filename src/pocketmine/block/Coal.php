<?php
namespace pocketmine\block;
use pocketmine\item\Item;
use pocketmine\item\Tool;
class Coal extends Solid{

	protected $id = self::COAL_BLOCK;

	public function __construct(){

	}

	public function getHardness() {
		return 5;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getBurnChance() {
		return 5;
	}

	public function getBurnAbility() {
		return 5;
	}

	public function getName() {
		return "Coal Block";
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= 1){
			return [
				[Item::COAL_BLOCK, 0, 1],
			];
		}else{
			return [];
		}
	}
}