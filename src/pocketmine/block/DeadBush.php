<?php
namespace pocketmine\block;
use pocketmine\level\Level;
use pocketmine\item\Item;
class DeadBush extends Flowable{

	protected $id = self::DEAD_BUSH;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Dead Bush";
	}


	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if($this->getSide(0)->isTransparent() === true){
				$this->getLevel()->useBreakOn($this);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return false;
	}


	public function getDrops(Item $item){
		if($item->isShears()){
			return [
				[Item::DEAD_BUSH, 0, 1],
			];
		}else{
			return [
				[Item::STICK, 0, mt_rand(0, 2)],
			];
		}
		
	}

}