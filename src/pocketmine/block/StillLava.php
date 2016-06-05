<?php

namespace pocketmine\block;
use pocketmine\level\Level;

class StillLava extends Lava{
	protected $id = self::STILL_LAVA;
	public function onUpdate($type){
		if($type == Level::BLOCK_UPDATE_NORMAL){
			parent::onUpdate($type);
		}
	}
	public function getName(){
		return "Still Lava";
	}
}
