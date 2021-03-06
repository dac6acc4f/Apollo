<?php
namespace pocketmine\block;
use pocketmine\item\Item;
class UnpoweredRepeater extends PoweredRepeater{
	protected $id = self::UNPOWERED_REPEATER;

	public function getName() {
		return "Unpowered Repeater";
	}

	public function getStrength(){
		return 0;
	}

	public function isActivated(Block $from = null){
		return false;
	}

	public function onBreak(Item $item){
		$this->getLevel()->setBlock($this, new Air(), true);
	}
}