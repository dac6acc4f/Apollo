<?php
namespace pocketmine\entity;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\MobEquipmentPacket;
use pocketmine\Player;
use pocketmine\item\Item as ItemItem;
class PigZombie extends Monster{
	const NETWORK_ID = 36;
	public $width = 0.6;
	public $length = 0.6;
	public $height = 1.8;
	public $drag = 0.2;
	public $gravity = 0.3;
	public $dropExp = [5, 5];
	public function getName(){
		return "PigZombie";
	}
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = PigZombie::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);
		parent::spawnTo($player);
		$pk = new MobEquipmentPacket();
		$pk->eid = $this->getId();
		$pk->item = new ItemItem(283);
		$pk->slot = 0;
		$pk->selectedSlot = 0;
		$player->dataPacket($pk);
	}
}