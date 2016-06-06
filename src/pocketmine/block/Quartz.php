<?php
namespace pocketmine\block;
use pocketmine\item\Item;
use pocketmine\item\Tool;
class Quartz extends Solid{
	
	const QUARTZ_NORMAL = 0;
	const QUARTZ_CHISELED = 1;
	const QUARTZ_PILLAR = 2;
	const QUARTZ_PILLAR2 = 3;


	protected $id = self::QUARTZ_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() {
		return 0.8;
	}

	public function getName(){
		static $names = [
			0 => "Quartz Block",
			1 => "Chiseled Quartz Block",
			2 => "Quartz Pillar",
			3 => "Quartz Pillar",
		];
		return $names[$this->meta & 0x03];
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= 1){
			return [
				[Item::QUARTZ_BLOCK, $this->meta & 0x03, 1],
			];
		}else{
			return [];
		}
	}
}