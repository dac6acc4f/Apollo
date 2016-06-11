<?php
namespace pocketmine\item;
class RabbitStew extends Food{
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::RABBIT_STEW, 0, $count, "Rabbit Stew");
	}
	public function getMaxStackSize(){
		return 1;
	}
	public function getFoodRestore(){
		return 10;
	}
	public function getSaturationRestore(){
		return 12;
	}
	public function getResidue(){
		return Item::get(Item::BOWL);
	}
}
