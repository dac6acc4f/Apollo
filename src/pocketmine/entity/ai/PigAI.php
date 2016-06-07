<?php
namespace pocketmine\entity\ai;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\entity\Pig;
use pocketmine\scheduler\CallbackTask;
use pocketmine\network\protocol\SetEntityMotionPacket;
class PigAI{
	private $AIHolder;
	public $width = 0.3;
	private $dif = 0;
	public function __construct(AIHolder $AIHolder){
		$this->AIHolder = $AIHolder;
		if($this->AIHolder->getServer()->aiConfig["pig"]){
			$this->AIHolder->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask ([
				$this,
				"PigRandomWalkCalc"
			]), 5);
			$this->AIHolder->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask ([
				$this,
				"PigRandomWalk"
			]), 1);
		}
	}
	public function PigRandomWalkCalc(){
		$this->dif = $this->AIHolder->getServer()->getDifficulty();
		foreach($this->AIHolder->getServer()->getLevels() as $level){
			foreach($level->getEntities() as $zo){
				if($zo::NETWORK_ID == Pig::NETWORK_ID){
					if($this->AIHolder->willMove($zo)){
						if(!isset($this->AIHolder->Pig[$zo->getId()])){
							$this->AIHolder->Pig[$zo->getId()] = array(
								'ID' => $zo->getId(),
								'IsChasing' => false,
								'motionx' => 0,
								'motiony' => 0,
								'motionz' => 0,
								'hurt' => 10,
								'time' => 10,
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
							$zom = &$this->AIHolder->Pig[$zo->getId()];
							$zom['x'] = $zo->getX();
							$zom['y'] = $zo->getY();
							$zom['z'] = $zo->getZ();
						}
						$zom = &$this->AIHolder->Pig[$zo->getId()];
						if($zom['gotimer'] == 0 or $zom['gotimer'] == 10){
							$newmx = mt_rand(-5, 5) / 10;
							while(abs($newmx - $zom['motionx']) >= 0.7){
								$newmx = mt_rand(-5, 5) / 10;
							}
							$zom['motionx'] = $newmx;
							$newmz = mt_rand(-5, 5) / 10;
							while(abs($newmz - $zom['motionz']) >= 0.7){
								$newmz = mt_rand(-5, 5) / 10;
							}
							$zom['motionz'] = $newmz;
						}elseif($zom['gotimer'] >= 20 and $zom['gotimer'] <= 24){
							$zom['motionx'] = 0;
							$zom['motionz'] = 0;
						}
						$zom['gotimer'] += 0.5;
						if($zom['gotimer'] >= 22) $zom['gotimer'] = 0;
						$zom['yup'] = 0;
						$zom['up'] = 0;
						$pos = new Vector3 ($zom['x'] + $zom['motionx'], floor($zo->getY()) + 1, $zom['z'] + $zom['motionz']);
						$zy = $this->AIHolder->ifjump($zo->getLevel(), $pos);
						if($zy === false){
							$pos2 = new Vector3 ($zom['x'], $zom['y'], $zom['z']);
							if($this->AIHolder->ifjump($zo->getLevel(), $pos2) === false){
								$zom['yup'] = 0;
							}else{
								$zom['motionx'] = -$zom['motionx'];
								$zom['motionz'] = -$zom['motionz'];
								$zom['up'] = 0;
							}
						}else{
							$pos2 = new Vector3 ($zom['x'] + $zom['motionx'], $zy - 1, $zom['z'] + $zom['motionz']);
							if($pos2->y - $zom['y'] < 0){
								$zom['up'] = 1;
							}else{
								$zom['up'] = 0;
							}
						}
						if($zom['motionx'] == 0 and $zom['motionz'] == 0){
						}else{
							$yaw = $this->AIHolder->getyaw($zom['motionx'], $zom['motionz']);
							$zom['yaw'] = $yaw;
							$zom['pitch'] = 0;
						}
						if(!$zom['knockBack']){
							$zom['x'] = $pos2->getX();
							$zom['z'] = $pos2->getZ();
							$zom['y'] = $pos2->getY();
						}
						$zom['motiony'] = $pos2->getY() - $zo->getY();
						$zo->setPosition($pos2);
					}
				}
			}
		}
	}
	public function PigRandomWalk(){
		foreach($this->AIHolder->getServer()->getLevels() as $level){
			foreach($level->getEntities() as $zo){
				if($zo::NETWORK_ID == Pig::NETWORK_ID){
					if(isset($this->AIHolder->Pig[$zo->getId()])){
						$zom = &$this->AIHolder->Pig[$zo->getId()];
						if($zom['canAttack'] != 0){
							$zom['canAttack'] -= 1;
						}
						$pos = $zo->getLocation();
						$downly = $zo->onGround;
						if(abs($zo->getY() - $zom['oldv3']->y) == 1 and $zom['canjump'] === true){
							$zom['canjump'] = false;
							$zom['jump'] = 0.3;
						}else{
							if($zom['jump'] > 0.01){
								$zom['jump'] -= 0.1;
							}else{
								$zom['jump'] = 0;
							}
						}
						$pk3 = new SetEntityMotionPacket;
						$pk3->entities = [
							[$zo->getID(), $zom['xxx'], $zom['jump'] - $downly ? 0.04 : 0, $zom['zzz']]
						];
						foreach($zo->getViewers() as $pl){
							$pl->dataPacket($pk3);
						}
					}
				}
			}
		}
	}
	public function array_clear(){
		if(count($this->AIHolder->Pig) != 0){
			foreach($this->AIHolder->Pig as $eid => $info){
				foreach($this->AIHolder->getServer()->getLevels() as $level){
					if(!($level->getEntity($eid) instanceof Entity)){
						unset($this->AIHolder->Pig[$eid]);
					}
				}
			}
		}
	}
}