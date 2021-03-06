<?php
namespace pocketmine\block;
use pocketmine\entity\Arrow;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityCombustByBlockEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;
class Fire extends Flowable{

	protected $id = self::FIRE;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function hasEntityCollision(){
		return true;
	}

	public function getName(){
		return "Fire Block";
	}

	public function getLightLevel(){
		return 15;
	}

	public function isBreakable(Item $item){
		return false;
	}

	public function canBeReplaced(){
		return true;
	}

	public function onEntityCollide(Entity $entity){
		if(!$entity->hasEffect(Effect::FIRE_RESISTANCE)){
			$ev = new EntityDamageByBlockEvent($this, $entity, EntityDamageEvent::CAUSE_FIRE, 1);
			$entity->attack($ev->getFinalDamage(), $ev);
		}

		$ev = new EntityCombustByBlockEvent($this, $entity, 8);
		if($entity instanceof Arrow){
			$ev->setCancelled();
		}
		Server::getInstance()->getPluginManager()->callEvent($ev);
		if(!$ev->isCancelled()){
			$entity->setOnFire($ev->getDuration());
		}
	}

	public function getDrops(Item $item){
		return [];
	}

	public function onUpdate($type){
		if($type == Level::BLOCK_UPDATE_NORMAL or $type = Level::BLOCK_UPDATE_RANDOM){
			if(!$this->isBlockTopFacingSurfaceSolid($this->getSide(Vector3::SIDE_DOWN)) and !$this->canNeighborBurn()){
				$this->getLevel()->setBlock($this, new Air(), true);
				return Level::BLOCK_UPDATE_NORMAL;
			}elseif($type == Level::BLOCK_UPDATE_SCHEDULED and $this->getLevel()->getServer()->fireSpread){
				$forever = $this->getSide(Vector3::SIDE_DOWN)->getId() == Block::NETHERRACK;

				if(!$this->isBlockTopFacingSurfaceSolid($this->getSide(Vector3::SIDE_DOWN)) and !$this->canNeighborBurn()){
					$this->getLevel()->setBlock($this, new Air(), true);
				}

				if(!$forever and $this->getLevel()->getWeather()->isRainy() and
					($this->getLevel()->canBlockSeeSky($this) or
						$this->getLevel()->canBlockSeeSky($this->getSide(Vector3::SIDE_EAST)) or
						$this->getLevel()->canBlockSeeSky($this->getSide(Vector3::SIDE_WEST)) or
						$this->getLevel()->canBlockSeeSky($this->getSide(Vector3::SIDE_SOUTH)) or
						$this->getLevel()->canBlockSeeSky($this->getSide(Vector3::SIDE_NORTH))
					)
				){
					$this->getLevel()->setBlock($this, new Air(), true);
				}else{
					$meta = $this->meta;

					if($meta < 15){
						$this->meta = $meta + mt_rand(0, 3);
						$this->getLevel()->setBlock($this, $this, true);
					}

					$this->getLevel()->scheduleUpdate($this, $this->getTickRate() + mt_rand(0, 10));

					if(!$forever and !$this->canNeighborBurn()){
						if(!$this->isBlockTopFacingSurfaceSolid($this->getSide(Vector3::SIDE_DOWN)) or $meta > 3){
							$this->getLevel()->setBlock($this, new Air(), true);
						}
					}else if(!$forever && !($this->getSide(Vector3::SIDE_DOWN)->getBurnAbility() > 0) && $meta == 15 && mt_rand(0, 4) == 0){
						$this->getLevel()->setBlock($this, new Air(), true);
					}else{
						$o = 0;

						$this->tryToCatchBlockOnFire($this->getSide(Vector3::SIDE_EAST), 300 + $o, $meta);
						$this->tryToCatchBlockOnFire($this->getSide(Vector3::SIDE_WEST), 300 + $o, $meta);
						$this->tryToCatchBlockOnFire($this->getSide(Vector3::SIDE_DOWN), 250 + $o, $meta);
						$this->tryToCatchBlockOnFire($this->getSide(Vector3::SIDE_UP), 250 + $o, $meta);
						$this->tryToCatchBlockOnFire($this->getSide(Vector3::SIDE_SOUTH), 300 + $o, $meta);
						$this->tryToCatchBlockOnFire($this->getSide(Vector3::SIDE_NORTH), 300 + $o, $meta);

						$tempVector = new Vector3(0, 0, 0);
						for($x = ($this->x - 1); $x <= ($this->x + 1); ++$x){
							for($z = ($this->z - 1); $z <= ($this->z + 1); ++$z){
								for($y = ($this->y -1); $y <= ($this->y + 4); ++$y){
									$k = 100;

									if($y > $this->y + 1){
										$k += ($y - ($this->y + 1)) * 100;
									}

									$chance = $this->getChanceOfNeighborsEncouragingFire($this->getLevel()->getBlock($tempVector->setComponents($x, $y, $z)));

									if($chance > 0){
										$t = ($chance + 40 + $this->getLevel()->getServer()->getDifficulty() * 7);

										if($t > 0 and mt_rand(0, $k) <= $t){
											$damage = min(15, $meta + mt_rand(0, 5) / 4);

											$this->getLevel()->setBlock($tempVector->setComponents($x, $y, $z), new Fire($damage), true);
											$this->getLevel()->scheduleUpdate($tempVector, $this->getTickRate());
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return 0;
	}

	public function getTickRate() {
		return 30;
	}

}