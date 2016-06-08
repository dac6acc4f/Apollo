<?php
namespace pocketmine\entity;
use pocketmine\nbt\tag\Byte;
use pocketmine\nbt\tag\Int;
use pocketmine\level\format\FullChunk;
use pocketmine\nbt\tag\Compound;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
class Villager extends Creature implements NPC, Ageable{
	const PROFESSION_FARMER = 0;
	const PROFESSION_LIBRARIAN = 1;
	const PROFESSION_PRIEST = 2;
	const PROFESSION_BLACKSMITH = 3;
	const PROFESSION_BUTCHER = 4;
	const PROFESSION_GENERIC = 5;
	const NETWORK_ID = 15;
	const DATA_PROFESSION_ID = 16;
	public $width = 0.6;
	public $length = 0.6;
	public $height = 1.8;
	public function getName(){
		return "Villager";
	}
	public function __construct(FullChunk $chunk, Compound $nbt){
		if(!isset($nbt->Profession)){
			$nbt->Profession = new Byte("Profession", mt_rand(0, 5));
		}
		parent::__construct($chunk, $nbt);
		$this->setDataProperty(self::DATA_PROFESSION_ID, self::DATA_TYPE_BYTE, $this->getProfession());
	}
	protected function initEntity(){
		parent::initEntity();
		if(!isset($this->namedtag->Profession)){
			$this->setProfession(self::PROFESSION_GENERIC);
		}
	}
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Villager::NETWORK_ID;
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
	}
	public function setProfession($profession){
		$this->namedtag->Profession = new Byte("Profession", $profession);
	}
	public function getProfession(){
		return (int) $this->namedtag["Profession"];
	}
	public function isBaby(){
		return $this->getDataFlag(self::DATA_AGEABLE_FLAGS, self::DATA_FLAG_BABY);
	}
}