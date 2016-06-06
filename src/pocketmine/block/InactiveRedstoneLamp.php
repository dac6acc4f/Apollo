<?php
namespace pocketmine\block;
class InactiveRedstoneLamp extends ActiveRedstoneLamp{
	protected $id = self::INACTIVE_REDSTONE_LAMP;

	public function getLightLevel(){
		return 0;
	}

	public function getName(){
		return "Inactive Redstone Lamp";
	}

	public function isLightedByAround(){
		return false;
	}

	public function turnOn(){
		$this->getLevel()->setBlock($this, new ActiveRedstoneLamp(), true, false);
		return true;
	}

	public function turnOff(){
		return true;
	}
}