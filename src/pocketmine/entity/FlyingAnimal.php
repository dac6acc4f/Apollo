<?php
namespace pocketmine\entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
abstract class FlyingAnimal extends Creature implements Ageable{
    protected $gravity = 0;
    protected $drag = 0.02;
    public $flyDirection = null;
    public $flySpeed = 0.5;
    public $highestY = 128;
    private $switchDirectionTicker = 0;
    public $switchDirectionTicks = 300;
    public function onUpdate($currentTick){
        if($this->closed !== false){
            return false;
        }
        if ($this->willMove(100) and $this->getLevel()->getServer()->aiEnabled) {
            if(++$this->switchDirectionTicker === $this->switchDirectionTicks){
                $this->switchDirectionTicker = 0;
                if(mt_rand(0, 100) < 50){
                    $this->flyDirection = null;
                }
            }
            $this->lastUpdate = $currentTick;
            $this->timings->startTiming();
            if($this->isAlive()){
                if($this->y > $this->highestY and $this->flyDirection !== null){
                    $this->flyDirection->y = -0.5;
                }
                $inAir = !$this->isInsideOfSolid() and !$this->isInsideOfWater();
                if(!$inAir){
                    $this->flyDirection = null;
                }
                if($this->flyDirection instanceof Vector3){
                    $this->setMotion($this->flyDirection->multiply($this->flySpeed));
                }else{
                    $this->flyDirection = $this->generateRandomDirection();
                    $this->flySpeed = mt_rand(50, 100) / 500;
                    $this->setMotion($this->flyDirection);
                }
                $this->move($this->motionX, $this->motionY, $this->motionZ);
                $this->updateMovement();
                $f = sqrt(($this->motionX ** 2) + ($this->motionZ ** 2));
                $this->yaw = (-atan2($this->motionX, $this->motionZ) * 180 / M_PI);
                $this->pitch = (-atan2($f, $this->motionY) * 180 / M_PI);
                if($this->onGround and $this->flyDirection instanceof Vector3){
                    $this->flyDirection->y *= -1;
                }
            }
        }
        parent::onUpdate($currentTick);
        $this->timings->stopTiming();

        return !$this->onGround or abs($this->motionX) > 0.00001 or abs($this->motionY) > 0.00001 or abs($this->motionZ) > 0.00001;
    }
    private function generateRandomDirection(){
        return new Vector3(mt_rand(-1000, 1000) / 1000, mt_rand(-500, 500) / 1000, mt_rand(-1000, 1000) / 1000);
    }
	public function initEntity(){
		parent::initEntity();
		if($this->getDataProperty(self::DATA_AGEABLE_FLAGS) === null){
			$this->setDataProperty(self::DATA_AGEABLE_FLAGS, self::DATA_TYPE_BYTE, 0);
		}
	}
	public function isBaby(){
		return $this->getDataFlag(self::DATA_AGEABLE_FLAGS, self::DATA_FLAG_BABY);
	}
    public function attack($damage, EntityDamageEvent $source){
        if($source->isCancelled()){
            return;
        }
        if ($source->getCause() == EntityDamageEvent::CAUSE_FALL) {
            $source->setCancelled();
            return;
        }
        parent::attack($damage, $source);
    }
}