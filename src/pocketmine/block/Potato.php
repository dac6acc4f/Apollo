<?php
namespace pocketmine\block;
use pocketmine\item\Item;
use pocketmine\item\enchantment\enchantment;
class Potato extends Crops{

	protected $id = self::POTATO_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Potato Block";
	}

	public function getDrops(Item $item){
		$drops = [];
		if($this->meta >= 0x07){
			$fortunel = $item->getEnchantmentLevel(Enchantment::TYPE_MINING_FORTUNE);
			$fortunel = $fortunel > 3 ? 3 : $fortunel;
			$drops[] = [Item::POTATO, 0, mt_rand(1, 4 + $fortunel)];
		}else{
			$drops[] = [Item::POTATO, 0, 1];
		}

		return $drops;
	}
}