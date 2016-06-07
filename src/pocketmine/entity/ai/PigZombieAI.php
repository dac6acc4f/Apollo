<?php
namespace pocketmine\entity\ai;
use pocketmine\entity\ai\AIHolder;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\math\Vector2;
use pocketmine\entity\Entity;
use pocketmine\entity\PigZombie as Zombie;
use pocketmine\scheduler\CallbackTask;
use pocketmine\network\protocol\SetEntityMotionPacket;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
class PigZombieAI{
	private $AIHolder;
	public $width = 0.4;
	private $dif = 0;
	public $hatred_r = 16;
	public $zo_hate_v = 1.4;
	public function __construct(AIHolder $AIHolder){
		$this->AIHolder = $AIHolder;
		if($this->AIHolder->getServer()->aiConfig["pigzombie"]){
			$this->AIHolder->getServer()->getScheduler ()->scheduleRepeatingTask ( new CallbackTask ( [
				$this,
				"ZombieRandomWalkCalc"
			] ), 10);
			$this->AIHolder->getServer()->getScheduler ()->scheduleRepeatingTask ( new CallbackTask ( [
				$this,
				"ZombieRandomWalk"
			] ), 1);
			$this->AIHolder->getServer()->getScheduler ()->scheduleRepeatingTask ( new CallbackTask ( [
				$this,
				"ZombieHateWalk"
			] ), 10);
			$this->AIHolder->getServer()->getScheduler ()->scheduleRepeatingTask ( new CallbackTask ( [
				$this,
				"ZombieHateFinder"
			] ), 10);
			$this->AIHolder->getServer()->getScheduler ()->scheduleRepeatingTask ( new CallbackTask ( [
				$this,
				"array_clear"
			] ), 20 * 5);
		}
	}
	public function array_clear() {
		if (count($this->AIHolder->pigzombie) != 0) {
			foreach ($this->AIHolder->pigzombie as $eid=> $info) {
				foreach ($this->AIHolder->getServer()->getLevels() as $level) {
					if (!($level->getEntity($eid) instanceof Entity)) {
						unset($this->AIHolder->pigzombie[$eid]);
					}
				}
			}
		}
	}
	public function ZombieRandomWalkCalc() {
		$this->dif = $this->AIHolder->getServer()->getDifficulty();
		foreach ($this->AIHolder->getServer()->getLevels() as $level) {
			foreach ($level->getEntities() as $zo){
				if($zo instanceof Zombie){
					if ($this->AIHolder->willMove($zo)) {
						if (!isset($this->AIHolder->pigzombie[$zo->getId()])){
							$this->AIHolder->pigzombie[$zo->getId()] = array(
								'ID' => $zo->getId(),
								'IsChasing' => false,
								'motionx' => 0,
								'motiony' => 0,
								'motionz' => 0,
								'hurt' => 10,
								'time'=>10,
								'x' => 0,
								'y' => 0,
								'z' => 0,
								'oldv3' => $zo->getLocation(),
								'yup' => 20,
								'up' => 0,
								'yaw' => $zo->yaw,
								'pitch' => 0,
								'level' => $zo->getLevel()->getName(),
								'xxx' => 0,
								'zzz' => 0,
								'gotimer' => 10,
								'swim' => 0,
								'jump' => 0.01,
								'canjump' => true,
								'drop' => false,
								'canAttack' => 0,
								'knockBack' => false,
							);
							$zom = &$this->AIHolder->pigzombie[$zo->getId()];
							$zom['x'] = $zo->getX();
							$zom['y'] = $zo->getY();
							$zom['z'] = $zo->getZ();
						}
						$zom = &$this->AIHolder->pigzombie[$zo->getId()];

						if ($zom['IsChasing'] === false){
							if ($zom['gotimer'] == 0 or $zom['gotimer'] == 10) {
								$newmx = mt_rand(-5,5)/10;
								while (abs($newmx - $zom['motionx']) >= 0.7) {
									$newmx = mt_rand(-5,5)/10;
								}
								$zom['motionx'] = $newmx;
								$newmz = mt_rand(-5,5)/10;
								while (abs($newmz - $zom['motionz']) >= 0.7) {
									$newmz = mt_rand(-5,5)/10;
								}
								$zom['motionz'] = $newmz;
							}
							elseif ($zom['gotimer'] >= 20 and $zom['gotimer'] <= 24) {
								$zom['motionx'] = 0;
								$zom['motionz'] = 0;
							}
							$zom['gotimer'] += 0.5;
							if ($zom['gotimer'] >= 22) $zom['gotimer'] = 0;
							$zom['yup'] = 0;
							$zom['up'] = 0;
							$pos = new Vector3 ($zom['x'] + $zom['motionx'], floor($zo->getY()) + 1,$zom['z'] + $zom['motionz']);
							$zy = $this->AIHolder->ifjump($zo->getLevel(),$pos);
							if ($zy === false){
								$pos2 = new Vector3 ($zom['x'], $zom['y'] ,$zom['z']);
								if ($this->AIHolder->ifjump($zo->getLevel(),$pos2) === false){
									$pos2 = new Vector3 ($zom['x'], $zom['y']-1,$zom['z']);
									$zom['up'] = 1;
									$zom['yup'] = 0;
								}
								else {
									$zom['motionx'] = - $zom['motionx'];
									$zom['motionz'] = - $zom['motionz'];
									$zom['up'] = 0;
								}
							}
							else {
								$pos2 = new Vector3 ($zom['x'] + $zom['motionx'], $zy - 1 ,$zom['z'] + $zom['motionz']);
								if ($pos2->y - $zom['y'] < 0) {
									$zom['up'] = 1;
								}
								else {
									$zom['up'] = 0;
								}
							}
							if ($zom['motionx'] == 0 and $zom['motionz'] == 0){
							}
							else {
								$yaw = $this->AIHolder->getyaw($zom['motionx'], $zom['motionz']);
								$zom['yaw'] = $yaw;
								$zom['pitch'] = 0;
							}
							if (!$zom['knockBack']) {
								$zom['x'] = $pos2->getX();
								$zom['z'] = $pos2->getZ();
								$zom['y'] = $pos2->getY();
							}
							$zom['motiony'] = $pos2->getY() - $zo->getY();
							$zo->setPosition($pos2->add(0,-1,0));
						}
					}
				}
			}
		}
	}
	public function ZombieHateFinder() {
		foreach ($this->AIHolder->getServer()->getLevels () as $level) {
			foreach ($level->getEntities() as $zo) {
				if ($zo instanceof Zombie) {
					if (isset($this->AIHolder->pigzombie[$zo->getId()])) {
						$zom = &$this->AIHolder->pigzombie[$zo->getId()];
						$h_r = $this->hatred_r;
						$pos = new Vector3($zo->getX(), $zo->getY(), $zo->getZ());
						$hatred = false;
						foreach ($zo->getViewers() as $p){
							if ($p->distance($pos) <= $h_r){
								if ($hatred === false) {
									$hatred = $p;
								} elseif ($hatred instanceof Player) {
									if ($p->distance($pos) <= $hatred->distance($pos)){
										$hatred = $p;
									}
								}
							}
						}
						if ($hatred == false or $this->dif == 0) {
							$zom['IsChasing'] = false;
						} else {
							$zom['IsChasing'] = $hatred->getName();
						}
					}
				}
			}
		}
	}
	public function ZombieHateWalk() {
		foreach ($this->AIHolder->getServer()->getLevels () as $level) {
			foreach ($level->getEntities() as $zo) {
				if ($zo instanceof Zombie) {
					if (isset($this->AIHolder->pigzombie[$zo->getId()])) {
						$zom = &$this->AIHolder->pigzombie[$zo->getId()];
						if (!$zom['knockBack']) {
							$zom['oldv3'] = $zo->getLocation();
							$zom['canjump'] = true;
							foreach ($level->getEntities() as $zo0) {
								if ($zo0 instanceof Zombie and !($zo0 == $zo)) {
									if ($zo->distance($zo0) <= $this->width * 2) {
										$dx = $zo->x - $zo0->x;
										$dz = $zo->z - $zo0->z;
										if ($dx == 0) {
											$dx = mt_rand(-5,5) / 5;
											if ($dx == 0) $dx = 1;
										}
										if ($dz == 0) {
											$dz = mt_rand(-5,5) / 5;
											if ($dz == 0) $dz = 1;
										}
										$zo->knockBack($zo0,0,$dx/5,$dz/5,0);
										$newpos = new Vector3($zo->x + $dx * 5, $zo->y, $zo->z + $dz * 5);
										$zom['x'] = $newpos->x;
										$zom['y'] = $newpos->y;
										$zom['z'] = $newpos->z;
										$this->AIHolder->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this->AIHolder,"knockBackover"],[$zo,$newpos]),5);
									}
								}
							}
							if ($zom['IsChasing'] !== false) {
								$p = $this->AIHolder->getServer()->getPlayer($zom['IsChasing']);
								if (($p instanceof Player) === false) {
									$zom['IsChasing'] = false;
								} else {
									$xx = $p->getX() - $zo->getX();
									$zz = $p->getZ() - $zo->getZ();
									$yaw = $this->AIHolder->getyaw($xx,$zz);
									if ($zz != 0) {
										$bi = $xx/$zz;
									}else{
										$bi = 0;
									}
									if ($zo->getHealth() == $zo->getMaxHealth()) {
										$zzz = sqrt(($this->zo_hate_v / 2.5) / ($bi * $bi + 1));
									}else{
										$zzz = sqrt(($this->zo_hate_v / 2) / ($bi * $bi + 1));
									}
									if ($zz < 0) $zzz = -$zzz;
									$xxx = $zzz * $bi;
									$zo_v2 = new Vector2($zo->getX(),$zo->getZ());
									$p_v2 = new Vector2($p->getX(),$p->getZ());
									if ($zo_v2->distance($p_v2) <= $this->zo_hate_v/2) {
										$xxx = $xx;
										$zzz = $zz;
									}
									$zom['xxx'] = $xxx;
									$zom['zzz'] = $zzz;
									$pos0 = new Vector3 ($zo->getX(), $zo->getY() + 1, $zo->getZ());
									$pos = new Vector3 ($zo->getX() + $xxx, $zo->getY() + 1, $zo->getZ() + $zzz);
									$zy = $this->AIHolder->ifjump($zo->getLevel(), $pos, true);
									if ($zy === false or ($zy !== false and $this->AIHolder->ifjump($zo->getLevel(), $pos0, true, true) == 'fall')){
										if ($this->AIHolder->ifjump($zo->getLevel(), $pos0, false) === false){
											if ($zom['drop'] === false) {
												$zom['drop'] = 0;
											}
											$pos2 = new Vector3 ($zo->getX(), $zo->getY() - ($zom['drop'] / 2 + 1.25), $zo->getZ());
										} else {
											$zom['drop'] = false;
											if ($this->AIHolder->whatBlock($level, $pos0) == "climb") {
												$zy = $pos0->y + 0.7;
												$pos2 = new Vector3 ($zo->getX(), $zy - 1, $zo->getZ());
											}
											elseif ($xxx != 0 and $zzz != 0) {
												if ($this->AIHolder->ifjump($zo->getLevel(), new Vector3($zo->getX() + $xxx, $zo->getY() + 1, $zo->getZ()), true) !== false) {
													$pos2 = new Vector3($zo->getX() + $xxx, floor($zo->getY()), $zo->getZ());
												} elseif ($this->AIHolder->ifjump($zo->getLevel(), new Vector3($zo->getX(), $zo->getY() + 1, $zo->getZ() + $zzz), true) !== false) {
													$pos2 = new Vector3($zo->getX(), floor($zo->getY()), $zo->getZ() + $zzz);
												} else {
													$pos2 = new Vector3 ($zo->getX() - $xxx / 5, floor($zo->getY()), $zo->getZ() - $zzz / 5);
												}
											} else {
												$pos2 = new Vector3 ($zo->getX() - $xxx / 5, floor($zo->getY()), $zo->getZ() - $zzz / 5);
											}
										}
									} else {
										$pos2 = new Vector3 ($zo->getX() + $xxx, $zy - 1, $zo->getZ() + $zzz);
									}
									$zo->setPosition($pos2->add(0,-1,0));
									$pos3 = $pos2;
									$pos3->y = $pos3->y + 2.62;
									$ppos = $p->getLocation();
									$ppos->y = $ppos->y + $p->getEyeHeight();
									$pitch = $this->AIHolder->getpitch($pos3,$ppos);
									$zom['yaw'] = $yaw;
									$zom['pitch'] = $pitch;
									if (!$zom['knockBack']) {
										$zom['x'] = $zo->getX();
										$zom['y'] = $zo->getY();
										$zom['z'] = $zo->getZ();
									}
								}
							}
						}
					}
				}
			}
		}
	}
	public function ZombieRandomWalk() {
		foreach ($this->AIHolder->getServer()->getLevels() as $level) {
			foreach ($level->getEntities() as $zo) {
				if ($zo instanceof Zombie) {
					if (isset($this->AIHolder->pigzombie[$zo->getId()])) {
						$zom = &$this->AIHolder->pigzombie[$zo->getId()];
						if ($zom['canAttack'] != 0) {
							$zom['canAttack'] -= 1;
						}
						$pos = $zo->getLocation();
						if ($zom['drop'] !== false) {
							$olddrop = $zom['drop'];
							$zom['drop'] += 0.5;
							$drop = $zom['drop'];
							$dropy = $zo->getY() - ($olddrop * 0.05 + 0.0125);
							$posd0 = new Vector3 (floor($zo->getX()), floor($dropy), floor($zo->getZ()));
							$posd = new Vector3 ($zo->getX(), $dropy, $zo->getZ());
							if ($this->AIHolder->whatBlock($zo->getLevel(), $posd0) == "air") {
								$zo->setPosition($posd->add(0,-1,0));
							} else {
								for ($i = 1; $i <= $drop; $i++) {
									$posd0->y++;
									if ($this->AIHolder->whatBlock($zo->getLevel(), $posd0) != "block") {
										$posd->y = $posd0->y;
										$zo->setPosition($posd->add(0,-1,0));
										$h = $zom['drop'] * $zom['drop'] / 20;
										$damage = $h - 3;
										if ($damage > 0) {
											$zo->setHealth($zo->getHealth() - $damage);
										}
										$zom['drop'] = false;
										break;
									}
								}
							}
						} else {
							$drop = 0;
						}
						if ($zom['IsChasing'] !== false) {
							if (!$zom['knockBack']) {
								$zom['up'] = 0;
								if ($this->AIHolder->whatBlock($level, $pos) == "water") {
									$zom['swim'] += 1;
									if ($zom['swim'] >= 20) $zom['swim'] = 0;
								} else {
									$zom['swim'] = 0;
								}
								if(abs($zo->getY() - $zom['oldv3']->y) == 1 and $zom['canjump'] === true){
									$zom['canjump'] = false;
									$zom['jump'] = 0.5;
								}
								else {
									if ($zom['jump'] > 0.01) {
										$zom['jump'] -= 0.1;
									}
									else {
										$zom['jump'] = 0.01;
									}
								}
								$pk3 = new SetEntityMotionPacket;
								$pk3->entities = [
									[$zo->getID(), $zom['xxx'] / 10, -$zom['swim'] / 100 + $zom['jump'] - $drop, $zom['zzz'] / 10]
								];
								foreach ($zo->getViewers() as $pl) {
									$pl->dataPacket($pk3);
								}
								$p = $this->AIHolder->getServer()->getPlayer($zom['IsChasing']);
								if ($p instanceof Player) {
									if ($p->distance($pos) <= 1.3) {
										if ($zo->fireTicks > 0) {
											$p->setOnFire(1);
										}
									}
									if ($p->distance($pos) <= 1.5) {
										if ($zom['canAttack'] == 0) {
											$zom['canAttack'] = 20;
											if ($p->isSurvival()) {
												$zoDamage = $this->AIHolder->getZombieDamage($zo->getHealth());
												$damage = $this->AIHolder->getPlayerDamage($p, $zoDamage);
												$p->attack($damage, new EntityDamageByEntityEvent($zo,$p,EntityDamageEvent::CAUSE_ENTITY_ATTACK,$damage));
											}
										}
									}
								}
							}
						} else {
							if ($zom['up'] == 1) {
								if ($zom['yup'] <= 10) {
									$pk3 = new SetEntityMotionPacket;
									$pk3->entities = [
										[$zo->getID(), $zom['motionx'] / 10, $zom['motiony'] / 10, $zom['motionz'] / 10]
									];
									foreach ($zo->getViewers() as $pl) {
										$pl->dataPacket($pk3);
									}
								} else {
									$pk3 = new SetEntityMotionPacket;
									$pk3->entities = [
										[$zo->getID(), $zom['motionx'] / 10 - $zom['motiony'] / 10, $zom['motionz'] / 10]
									];
									foreach ($zo->getViewers() as $pl) {
										$pl->dataPacket($pk3);
									}
								}
							} else {
								$pk3 = new SetEntityMotionPacket;
								$pk3->entities = [
									[$zo->getID(), $zom['motionx'] / 10, -$zom['motiony'] / 10, $zom['motionz'] / 10]
								];
								foreach ($zo->getViewers() as $pl) {
									$pl->dataPacket($pk3);
								}
							}
						}
					}
				}
			}
		}
	}
	public function ZombieFire() {
		foreach ($this->AIHolder->getServer()->getLevels() as $level) {
			foreach ($level->getEntities() as $zo){
				if ($zo instanceof Zombie) {
					if(0 < $level->getTime() and $level->getTime() < 13500){
						$v3 = new Vector3($zo->getX(), $zo->getY(), $zo->getZ());
						$ok = true;
						for ($y0 = $zo->getY() + 2; $y0 <= $zo->getY()+10; $y0++) {
							$v3->y = $y0;
							if ($level->getBlock($v3)->getID() != 0) {
								$ok = false;
								break;
							}
						}
						if ($this->AIHolder->whatBlock($level,new Vector3($zo->getX(), floor($zo->getY() - 1), $zo->getZ())) == "water") $ok = false;
						if ($ok) $zo->setOnFire(2);
					}
				}
			}
		}
	}
}