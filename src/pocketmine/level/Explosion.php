<?php
namespace pocketmine\level;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Math;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\Byte as ByteTag;
use pocketmine\nbt\tag\Compound as CompoundTag;
use pocketmine\nbt\tag\Double as DoubleTag;
use pocketmine\nbt\tag\Enum as ListTag;
use pocketmine\nbt\tag\Float as FloatTag;
use pocketmine\network\Network;
use pocketmine\network\protocol\ExplodePacket;
use pocketmine\utils\Random;
class Explosion{
	private $rays = 16; //Rays
	public $level;
	public $source;
	public $size;
	/**
	 * @var Block[]
	 */
	public $affectedBlocks = [];
	public $stepLen = 0.3;
	/** @var Entity|Block */
	private $what;
	public function __construct(Position $center, $size, $what = null){
		$this->level = $center->getLevel();
		$this->source = $center;
		$this->size = max($size, 0);
		$this->what = $what;
	}
	public function canBeActivated(){
		return true;
	}
	/**
	 * @deprecated
	 * @return bool
	 */
	public function explode(){
		if($this->explodeA()){
			return $this->explodeB();
		}
		return false;
	}
	/**
	 * @return bool
	 */
	public function explodeA(){
		if($this->size < 0.1){
			return false;
		}
		$vector = new Vector3(0, 0, 0);
		$vBlock = new Vector3(0, 0, 0);
		$mRays = intval($this->rays - 1);
		for($i = 0; $i < $this->rays; ++$i){
			for($j = 0; $j < $this->rays; ++$j){
				for($k = 0; $k < $this->rays; ++$k){
					if($i === 0 or $i === $mRays or $j === 0 or $j === $mRays or $k === 0 or $k === $mRays){
						$vector->setComponents($i / $mRays * 2 - 1, $j / $mRays * 2 - 1, $k / $mRays * 2 - 1);
						$vector->setComponents(($vector->x / ($len = $vector->length())) * $this->stepLen, ($vector->y / $len) * $this->stepLen, ($vector->z / $len) * $this->stepLen);
						$pointerX = $this->source->x;
						$pointerY = $this->source->y;
						$pointerZ = $this->source->z;
							$x = floor($pointerX);
							$y = floor($pointerY);
							$z = floor($pointerZ);
							$block = $this->level->getBlock($vBlock);
							if($block->getId() !== 0){
								$blastForce -= ($block->getResistance() / 5 + 0.3) * $this->stepLen;
								if($blastForce > 0){
									if(!isset($this->affectedBlocks[$index = Level::blockHash($block->x, $block->y, $block->z)])){
										$this->affectedBlocks[$index] = $block;
									}
								}
							}
							$pointerX += $vector->x;
							$pointerY += $vector->y;
							$pointerZ += $vector->z;
						}
					}
				}
			}
		}
		return true;
	}
	public function explodeB(){
		$send = [];
		$updateBlocks = [];
		$source = (new Vector3($this->source->x, $this->source->y, $this->source->z))->floor();
		$yield = (1 / $this->size) * 100;
		if($this->what instanceof Entity){
			$this->level->getServer()->getPluginManager()->callEvent($ev = new EntityExplodeEvent($this->what, $this->source, $this->affectedBlocks, $yield));
				$yield = $ev->getYield();
				$this->affectedBlocks = $ev->getBlockList();
			}
		}
				if($this->what instanceof Entity){
					$ev = new EntityDamageByEntityEvent($this->what, $entity, EntityDamageEvent::CAUSE_ENTITY_EXPLOSION, $damage);
				}elseif($this->what instanceof Block){
					$ev = new EntityDamageByBlockEvent($this->what, $entity, EntityDamageEvent::CAUSE_BLOCK_EXPLOSION, $damage);
				}else{
					$ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_BLOCK_EXPLOSION, $damage);
				}
				$entity->setMotion($motion->multiply($impact));
		$air = Item::get(Item::AIR);
		foreach($this->affectedBlocks as $block){
			if($block->getId() === Block::TNT){
				$tnt = Entity::createEntity("PrimedTNT", $this->level->getChunk($block->x >> 4, $block->z >> 4), new CompoundTag("", [
					"Pos" => new ListTag("Pos", [
						new DoubleTag("", $block->x + 0.5),
						new DoubleTag("", $block->y),
						new DoubleTag("", $block->z + 0.5)
					]),
					"Motion" => new ListTag("Motion", [
						new DoubleTag("", -sin($mot) * 0.02),
						new DoubleTag("", 0.2),
						new DoubleTag("", -cos($mot) * 0.02)
					]),
					"Rotation" => new ListTag("Rotation", [
						new FloatTag("", 0),
						new FloatTag("", 0)
					]),
					"Fuse" => new ByteTag("Fuse", mt_rand(10, 11))
				]));
			$send[] = new Vector3($block->x - $source->x, $block->y - $source->y, $block->z - $source->z);
		}
		$pk = new ExplodePacket();
		$pk->x = $this->source->x;
		$pk->y = $this->source->y;
		$pk->z = $this->source->z;
		$pk->radius = 10;
		$pk->records = $send;
		$this->level->addChunkPacket($source->x >> 4, $source->z >> 4, $pk);
		return true;
	}
}
