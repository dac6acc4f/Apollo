<?php
namespace pocketmine\block;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\Player;
use pocketmine\entity\IronGolem;
use pocketmine\entity\SnowGolem;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Double;
use pocketmine\nbt\tag\Float;
class Pumpkin extends Solid{

	protected $id = self::PUMPKIN;

	public function __construct(){

	}

	public function getHardness() {
		return 1;
	}
	
	public function isHelmet(){
		return true;
	}

	public function getToolType(){
		return Tool::TYPE_AXE;
	}

	public function getName(){
		return "Pumpkin";
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($player instanceof Player){
			$this->meta = ((int) $player->getDirection() + 5) % 4;
		}
		$this->getLevel()->setBlock($block, $this, true, true);
		if($player != null) {
			$level = $this->getLevel();
			if($player->getServer()->allowSnowGolem) {
				$block0 = $level->getBlock($block->add(0,-1,0));
				$block1 = $level->getBlock($block->add(0,-2,0));
				if($block0->getId() == Item::SNOW_BLOCK and $block1->getId() == Item::SNOW_BLOCK) {
					$level->setBlock($block, new Air());
					$level->setBlock($block0, new Air());
					$level->setBlock($block1, new Air());
					$golem = new SnowGolem($player->getLevel()->getChunk($this->getX() >> 4, $this->getZ() >> 4), new Compound("", [
						"Pos" => new Enum("Pos", [
							new Double("", $this->x),
							new Double("", $this->y),
							new Double("", $this->z)
						]),
						"Motion" => new Enum("Motion", [
							new Double("", 0),
							new Double("", 0),
							new Double("", 0)
						]),
						"Rotation" => new Enum("Rotation", [
							new Float("", 0),
							new Float("", 0)
						]),
					]));
					$golem->spawnToAll();
				}
			}
			if($player->getServer()->allowIronGolem) {
				$block0 = $level->getBlock($block->add(0,-1,0));
				$block1 = $level->getBlock($block->add(0,-2,0));
				$block2 = $level->getBlock($block->add(-1,-1,0));
				$block3 = $level->getBlock($block->add(1,-1,0));
				$block4 = $level->getBlock($block->add(0,-1,-1));
				$block5 = $level->getBlock($block->add(0,-1,1));
				if($block0->getId() == Item::IRON_BLOCK and $block1->getId() == Item::IRON_BLOCK) {
					if($block2->getId() == Item::IRON_BLOCK and $block3->getId() == Item::IRON_BLOCK and $block4->getId() == Item::AIR and $block5->getId() == Item::AIR) {
						$level->setBlock($block2, new Air());
						$level->setBlock($block3, new Air());
					}elseif($block4->getId() == Item::IRON_BLOCK and $block5->getId() == Item::IRON_BLOCK and $block2->getId() == Item::AIR and $block3->getId() == Item::AIR){
						$level->setBlock($block4, new Air());
						$level->setBlock($block5, new Air());
					}else return;
					$level->setBlock($block, new Air());
					$level->setBlock($block0, new Air());
					$level->setBlock($block1, new Air());
					$golem = new IronGolem($player->getLevel()->getChunk($this->getX() >> 4, $this->getZ() >> 4), new Compound("", [
						"Pos" => new Enum("Pos", [
							new Double("", $this->x),
							new Double("", $this->y),
							new Double("", $this->z)
						]),
						"Motion" => new Enum("Motion", [
							new Double("", 0),
							new Double("", 0),
							new Double("", 0)
						]),
						"Rotation" => new Enum("Rotation", [
							new Float("", 0),
							new Float("", 0)
						]),
					]));
					$golem->spawnToAll();
				}
			}
		}

		return true;
	}

}