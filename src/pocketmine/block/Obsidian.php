<?php
namespace pocketmine\block;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
class Obsidian extends Solid{

	protected $id = self::OBSIDIAN;

	private $temporalVector = null;

	public function __construct(){
		if($this->temporalVector === null){
			$this->temporalVector = new Vector3(0, 0, 0);
		}
	}

	public function getName(){
		return "Obsidian";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getHardness() {
		return 50;
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= 5){
			return [
				[Item::OBSIDIAN, 0, 1],
			];
		}else{
			return [];
		}
	}
	
	public function onBreak(Item $item) {
		parent::onBreak($item);
		
		if($this->getLevel()->getServer()->netherEnabled){
			for($i = 0;$i <= 6;$i++){
				if($i == 6){
					return;
				}elseif($this->getLevel()->getBlock($this->getSide($i))->getId() == 90){
					$side = $i;
					break;
				}
			}
			$block = $this->getLevel()->getBlock($this->getSide($i));
			if($this->getLevel()->getBlock($block->add(-1, 0, 0))->getId() == 90 or $this->getLevel()->getBlock($block->add(1, 0, 0))->getId() == 90){
				for($x = $block->getX();$this->getLevel()->getBlock($this->temporalVector->setComponents($x, $block->getY(), $block->getZ()))->getId() == 90;$x++){
					for($y = $block->getY();$this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->getZ()))->getId() == 90;$y++){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->getZ()), new Block(0, 0));
					}
					for($y = $block->getY() - 1;$this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->getZ()))->getId() == 90;$y--){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->getZ()), new Block(0, 0));
					}
				}
				for($x = $block->getX() - 1;$this->getLevel()->getBlock($this->temporalVector->setComponents($x, $block->getY(), $block->getZ()))->getId() == 90;$x--){
					for($y = $block->getY();$this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->getZ()))->getId() == 90;$y++){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->getZ()), new Block(0, 0));
					}
					for($y = $block->getY() - 1;$this->getLevel()->getBlock($this->temporalVector->setComponents($x, $y, $block->getZ()))->getId() == 90;$y--){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($x, $y, $block->getZ()), new Block(0, 0));
					}
				}
			}else{//z方向
				for($z = $block->getZ();$this->getLevel()->getBlock($this->temporalVector->setComponents($block->getX(), $block->getY(), $z))->getId() == 90;$z++){
					for($y = $block->getY();$this->getLevel()->getBlock($this->temporalVector->setComponents($block->getX(), $y, $z))->getId() == 90;$y++){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($block->getX(), $y, $z), new Block(0, 0));
					}
					for($y = $block->getY() - 1;$this->getLevel()->getBlock($this->temporalVector->setComponents($block->getX(), $y, $z))->getId() == 90;$y--){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($block->getX(), $y, $z), new Block(0, 0));
					}
				}
				for($z = $block->getZ() - 1;$this->getLevel()->getBlock($this->temporalVector->setComponents($block->getX(), $block->getY(), $z))->getId() == 90;$z--){
					for($y = $block->getY();$this->getLevel()->getBlock($this->temporalVector->setComponents($block->getX(), $y, $z))->getId() == 90;$y++){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($block->getX(), $y, $z), new Block(0, 0));
					}
					for($y = $block->getY() - 1;$this->getLevel()->getBlock($this->temporalVector->setComponents($block->getX(), $y, $z))->getId() == 90;$y--){
						$this->getLevel()->setBlock($this->temporalVector->setComponents($block->getX(), $y, $z), new Block(0, 0));
					}
				}
			}
		}
	}
}