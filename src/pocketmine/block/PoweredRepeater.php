<?php
namespace pocketmine\block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
class PoweredRepeater extends RedstoneSource{
	protected $id = self::POWERED_REPEATER;

	const ACTION_ACTIVATE = "Repeater Activate";
	const ACTION_DEACTIVATE = "Repeater Deactivate";

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName() {
		return "Powered Repeater";
	}

	public function canBeActivated() {
		return true;
	}

	public function getStrength(){
		return 15;
	}

	public function getDirection() {
		$direction = 0;
		switch($this->meta % 4){
			case 0:
				$direction = 3;
				break;
			case 1:
				$direction = 4;
				break;
			case 2:
				$direction = 2;
				break;
			case 3:
				$direction = 5;
				break;
		}
		return $direction;
	}
	
	public function getOppositeDirection() {
		return $this->getOppositeSide($this->getDirection());
	}

	public function getDelayLevel() {
		return round(($this->meta - ($this->meta % 4)) / 4) + 1;
	}

	public function isActivated(Block $from = null){
		if(!$from instanceof Block){
			return false;
		}else{
			if($this->y != $from->y){
				return false;
			}
			if($from->equals($this->getSide($this->getOppositeDirection()))){
				return true;
			}
			return false;
		}
	}

	public function activate(array $ignore = []){
		if($this->canCalc()){
			if($this->id != self::POWERED_REPEATER){
				$this->id = self::POWERED_REPEATER;
				$this->getLevel()->setBlock($this, $this, true, false);
			}
			$this->getLevel()->setBlockTempData($this, self::ACTION_ACTIVATE);
			$this->getLevel()->scheduleUpdate($this, $this->getDelayLevel() * $this->getLevel()->getServer()->getTicksPerSecondAverage() / 10);
		}
	}

	public function deactivate(array $ignore = []){
		if($this->canCalc()){
			if($this->id != self::UNPOWERED_REPEATER){
				$this->id = self::UNPOWERED_REPEATER;
				$this->getLevel()->setBlock($this, $this, true, false);
			}
			$this->getLevel()->setBlockTempData($this, self::ACTION_DEACTIVATE);
			$this->getLevel()->scheduleUpdate($this, $this->getDelayLevel() * $this->getLevel()->getServer()->getTicksPerSecondAverage() / 10);
		}
	}

	public function deactivateImmediately(){
		$this->deactivateBlock($this->getSide($this->getOppositeDirection()));
		$this->deactivateBlock($this->getSide(Vector3::SIDE_DOWN, 2));//TODO: improve
	}

	public function onUpdate($type){
		if($type == Level::BLOCK_UPDATE_SCHEDULED){
			if($this->getLevel()->getBlockTempData($this) == self::ACTION_ACTIVATE){
				$this->activateBlock($this->getSide($this->getOppositeDirection()));
				$this->activateBlock($this->getSide(Vector3::SIDE_DOWN, 2));
			}elseif($this->getLevel()->getBlockTempData($this) == self::ACTION_DEACTIVATE){
				$this->deactivateImmediately();
			}
			$this->getLevel()->setBlockTempData($this);
		}
		return $type;
	}

	public function onActivate(Item $item, Player $player = null){
		$meta = $this->meta + 4;
		if($meta > 15) $this->meta = $this->meta % 4;
		else $this->meta = $meta;
		$this->getLevel()->setBlock($this, $this, true, false);
		return true;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($player instanceof Player){
			$this->meta = ((int) $player->getDirection() + 5) % 4;
		}
		$this->getLevel()->setBlock($block, $this, true, false);
		if($this->checkPower($this)){
			$this->activate();
		}
	}

	public function onBreak(Item $item){
		$this->deactivateImmediately();
		$this->getLevel()->setBlock($this, new Air(), true, false);
		$this->getLevel()->setBlockTempData($this);
	}

	public function getDrops(Item $item) {
		return [
			[Item::REPEATER, 0, 1]
		];
	}
}