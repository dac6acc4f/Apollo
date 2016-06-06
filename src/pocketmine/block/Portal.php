<?php
namespace pocketmine\block;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\level\particle\PortalParticle;
use pocketmine\Player;
use pocketmine\math\Vector3;
class Portal extends Transparent{

	protected $id = self::PORTAL;

	private $temporalVector = null;
	
	public function __construct(){
		if($this->temporalVector === null){
			$this->temporalVector = new Vector3(0, 0, 0);
		}
	}

	public function getName(){
		return "Portal";
	}
	
	public function getHardness() {
		return -1;
	}

	public function getResistance(){
		return 0;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function canBeActivated(){
		return true;
	}

	public function hasEntityCollision(){
		return true;
	}

	public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			for($n = 0;$n <= 2;$n++){
				$sound = new EndermanTeleportSound($this);
				$this->getLevel()->addSound($sound);
			}
			
			for($num = 0;$num <= 10;$num++){
				$particle = new PortalParticle($this);
				$this->getLevel()->addParticle($particle);
			}
		}

		return true;
	}

	public function onBreak(Item $item) {
		$sound = new EndermanTeleportSound($this);
		$this->getLevel()->addSound($sound);
		$particle = new PortalParticle($this);
		$this->getLevel()->addParticle($particle);
		$block = $this;
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
		}else{
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
		parent::onBreak($item);
	}
	
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($player instanceof Player){
			$this->meta = ((int) $player->getDirection() + 5) % 2;
		}
		$this->getLevel()->setBlock($block, $this, true, true);

		return true;
	}
	
	public function getDrops(Item $item){
		if($item->isPickaxe() >= 1){
			return [
				[Item::PORTAL, 0, 1],
			];
		}else{
			return [];
		}
	}
}