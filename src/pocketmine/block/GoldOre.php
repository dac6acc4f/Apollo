<?php
namespace pocketmine\block;
use pocketmine\item\Item;
use pocketmine\item\Tool;
class GoldOre extends Solid{

	protected $id = self::GOLD_ORE;

	public function __construct(){

	}

	public function getName(){
		return "Gold Ore";
	}

	public function getHardness() {
		return 3;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= 4){
			return [
				[Item::GOLD_ORE, 0, 1],
			];
		}else{
			return [];
		}
	}
}