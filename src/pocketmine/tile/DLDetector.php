<?php

namespace pocketmine\tile;

use pocketmine\block\Block;
use pocketmine\block\DaylightDetector;
use pocketmine\level\format\FullChunk;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\String;
use pocketmine\nbt\tag\Int;
use pocketmine\level\Level;

class DLDetector extends Spawnable{
	private $lastType = 0;

	public function __construct(FullChunk $chunk, Compound $nbt){
		parent::__construct($chunk, $nbt);
		$this->scheduleUpdate();
	}

	public function getLightByTime(){
		$time = $this->getLevel()->getTime();
		if(($time >= Level::TIME_DAY and $time <= Level::TIME_SUNSET) or
			($time >= Level::TIME_SUNRISE and $time <= Level::TIME_FULL)) return 15;
		return 0;
	}

	public function isActivated(){
		if($this->getType() == Block::DAYLIGHT_SENSOR) {
			if($this->getLightByTime() == 15) return true;
			return false;
		}else{
			if($this->getLightByTime() == 0) return true;
			return false;
		}
	}

	private function getType(){
		return $this->getBlock()->getId();
	}

	public function onUpdate(){
		if(($this->getLevel()->getServer()->getTick() % 3) == 0){ //Update per 3 ticks
			if($this->getType() != $this->lastType){ //Update when changed
				/** @var DaylightDetector $block */
				$block = $this->getBlock();
				if($this->isActivated()){
					$block->activate();
				}else{
					$block->deactivate();
				}
				$this->lastType = $block->getId();
			}
		}
		return true;
	}

	public function getSpawnCompound(){
		return new Compound("", [
			new String("id", Tile::DAY_LIGHT_DETECTOR),
			new Int("x", floor $this->x),
			new Int("y", floor $this->y),
			new Int("z", floor $this->z),
		]);
	}
}
