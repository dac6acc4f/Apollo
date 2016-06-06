<?php
namespace pocketmine\block;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\item\enchantment\enchantment;
class PackedIce extends Solid {

	protected $id = self::PACKED_ICE;

	public function __construct() {

	}

	public function getName() {
		return "Packed Ice";
	}

	public function getHardness() {
		return 0.5;
	}

	public function getToolType() {
		return Tool::TYPE_PICKAXE;
	}

	public function getDrops(Item $item) {
		if($item->getEnchantmentLevel(Enchantment::TYPE_MINING_SILK_TOUCH) > 0){
			return [
				[Item::PACKED_ICE, 0, 1],
			];
		}else{
			return [];
		}
	}
} 
