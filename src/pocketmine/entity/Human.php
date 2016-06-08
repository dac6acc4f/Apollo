<?php
namespace pocketmine\entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\Item as ItemItem;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\Byte;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Float;
use pocketmine\nbt\tag\Int;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Short;
use pocketmine\nbt\tag\String;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\RemovePlayerPacket;
use pocketmine\Player;
use pocketmine\utils\UUID;
class Human extends Creature implements ProjectileSource, InventoryHolder{
	const DATA_PLAYER_FLAG_SLEEP = 1;
	const DATA_PLAYER_FLAG_DEAD = 2;
	const DATA_PLAYER_FLAGS = 16;
	const DATA_PLAYER_BED_POSITION = 17;
	protected $inventory;
	protected $uuid;
	protected $rawUUID;
	public $width = 0.6;
	public $length = 0.6;
	public $height = 1.8;
	public $eyeHeight = 1.62;
	protected $skinName;
	protected $skin;
	protected $foodTickTimer = 0;
	protected $totalXp = 0;
	protected $xpSeed;
	public function getSkinData(){
		return $this->skin;
	}
	public function getSkinName(){
		return $this->skinName;
	}
	public function getUniqueId(){
		return $this->uuid;
	}
	public function getRawUniqueId(){
		return $this->rawUUID;
	}
	public function setSkin($str, $skinName){
		$this->skin = $str;
		$this->skinName = $skinName;
	}
	public function getFood() {
		return (float) $this->attributeMap->getAttribute(Attribute::HUNGER)->getValue();
	}
	public function setFood($new){
		$attr = $this->attributeMap->getAttribute(Attribute::HUNGER);
		$old = $attr->getValue();
		$attr->setValue($new);
		foreach([17, 6, 0] as $bound){
			if(($old > $bound) !== ($new > $bound)){
				$reset = true;
			}
		}
		if(isset($reset)){
			$this->foodTickTimer = 0;
		}

	}
	public function getMaxFood() {
		return (float) $this->attributeMap->getAttribute(Attribute::HUNGER)->getMaxValue();
	}
	public function addFood($amount){
		$attr = $this->attributeMap->getAttribute(Attribute::HUNGER);
		$amount += $attr->getValue();
		$amount = max(min($amount, $attr->getMaxValue()), $attr->getMinValue());
		$this->setFood($amount);
	}
	public function getSaturation() {
		return (float) $this->attributeMap->getAttribute(Attribute::SATURATION)->getValue();
	}
	public function setSaturation($saturation){
		$this->attributeMap->getAttribute(Attribute::SATURATION)->setValue((float)$saturation);
	}
	public function addSaturation($amount){
		$attr = $this->attributeMap->getAttribute(Attribute::SATURATION);
		$attr->setValue($attr->getValue() + $amount, true);
	}
	public function getExhaustion() {
		return (float) $this->attributeMap->getAttribute(Attribute::EXHAUSTION)->getValue();
	}
	public function setExhaustion($exhaustion){
		$this->attributeMap->getAttribute(Attribute::EXHAUSTION)->setValue((float)$exhaustion);
	}
	public function exhaust($amount,$cause = PlayerExhaustEvent::CAUSE_CUSTOM){
		$this->server->getPluginManager()->callEvent($ev = new PlayerExhaustEvent($this, $amount, $cause));
		if($ev->isCancelled()){
			return 0.0;
		}
		$exhaustion = $this->getExhaustion();
		$exhaustion += $ev->getAmount();
		while($exhaustion >= 4.0){
			$exhaustion -= 4.0;
			$saturation = $this->getSaturation();
			if($saturation > 0){
				$saturation = max(0, $saturation - 1.0);
				$this->setSaturation($saturation);
			}else{
				$food = $this->getFood();
				if($food > 0){
					$food--;
					$this->setFood($food);
				}
			}
		}
		$this->setExhaustion($exhaustion);
		return $ev->getAmount();
	}
	public function getXpLevel(){
		return (int) $this->attributeMap->getAttribute(Attribute::EXPERIENCE_LEVEL)->getValue();
	}
	public function setXpLevel($level){
		$this->attributeMap->getAttribute(Attribute::EXPERIENCE_LEVEL)->setValue($level);
	}
	public function getXpProgress(){
		return $this->attributeMap->getAttribute(Attribute::EXPERIENCE)->getValue();
	}
	public function setXpProgress($progress){
		$this->attributeMap->getAttribute(Attribute::EXPERIENCE)->setValue($progress);
	}
	public function getTotalXp(){
		return $this->totalXp;
	}
	public function getRemainderXp(){
		return $this->getTotalXp() - self::getTotalXpForLevel($this->getXpLevel());
	}
	public function recalculateXpProgress(){
		$this->setXpProgress($this->getRemainderXp() / self::getTotalXpForLevel($this->getXpLevel()));
	}
	public static function getTotalXpForLevel($level){
		if($level <= 16){
			return $level ** 2 + $level * 6;
		}elseif($level < 32){
			return $level ** 2 * 2.5 - 40.5 * $level + 360;
		}
		return $level ** 2 * 4.5 - 162.5 * $level + 2220;
	}
	public function getInventory(){
		return $this->inventory;
	}
	protected function initEntity(){
		$this->setDataFlag(self::DATA_PLAYER_FLAGS, self::DATA_PLAYER_FLAG_SLEEP, false);
		$this->setDataProperty(self::DATA_PLAYER_BED_POSITION, self::DATA_TYPE_POS, [0, 0, 0]);
		$this->inventory = new PlayerInventory($this);
		if($this instanceof Player){
			$this->addWindow($this->inventory, 0);
		}else{
			if(isset($this->namedtag->NameTag)){
				$this->setNameTag($this->namedtag["NameTag"]);
			}
			if(isset($this->namedtag->Skin) and $this->namedtag->Skin instanceof Compound){
				$this->setSkin($this->namedtag->Skin["Data"], $this->namedtag->Skin["Name"]);
			}
			$this->uuid = UUID::fromData($this->getId(), $this->getSkinData(), $this->getNameTag());
		}
		if(isset($this->namedtag->Inventory) and $this->namedtag->Inventory instanceof Enum){
			foreach($this->namedtag->Inventory as $item){
				if($item["Slot"] >= 0 and $item["Slot"] < 9){
					$this->inventory->setHotbarSlotIndex($item["Slot"], isset($item["TrueSlot"]) ? $item["TrueSlot"] : -1);
				}elseif($item["Slot"] >= 100 and $item["Slot"] < 104){
					$this->inventory->setItem($this->inventory->getSize() + $item["Slot"] - 100, NBT::getItemHelper($item));
				}else{
					$this->inventory->setItem($item["Slot"] - 9, NBT::getItemHelper($item));
				}
			}
		}
		parent::initEntity();
		if(!isset($this->namedtag->XpLevel) or !($this->namedtag->XpLevel instanceof Int)){
			$this->namedtag->XpLevel = new Int("XpLevel", $this->getXpLevel());
		}else{
			$this->setXpLevel($this->namedtag["XpLevel"]);
		}
		if(!isset($this->namedtag->XpP) or !($this->namedtag->XpP instanceof Float)){
			$this->namedtag->XpP = new Float("XpP", $this->getXpProgress());
		}
		if(!isset($this->namedtag->XpTotal) or !($this->namedtag->XpTotal instanceof Int)){
			$this->namedtag->XpTotal = new Int("XpTotal", $this->totalXp);
		}else{
			$this->totalXp = $this->namedtag["XpTotal"];
		}
		if(!isset($this->namedtag->XpSeed) or !($this->namedtag->XpSeed instanceof Int)){
			$this->namedtag->XpSeed = new Int("XpSeed", isset($this->xpSeed) ? $this->xpSeed : ($this->xpSeed = mt_rand(1,5)));
		}else{
			$this->xpSeed = $this->namedtag["XpSeed"];
		}
	}
	protected function addAttributes(){
		parent::addAttributes();
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::SATURATION));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::EXHAUSTION));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::HUNGER));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::EXPERIENCE_LEVEL));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::EXPERIENCE));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::HEALTH));
		$this->attributeMap->addAttribute(Attribute::getAttribute(Attribute::MOVEMENT_SPEED));
	}
	public function entityBaseTick($tickDiff = 1){
		$hasUpdate = parent::entityBaseTick($tickDiff);
		return $hasUpdate;
	}
	public function getName(){
		return $this->getNameTag();
	}
	public function getDrops(){
		$drops = [];
		if($this->inventory !== null){
			foreach($this->inventory->getContents() as $item){
				$drops[] = $item;
			}
		}
		return $drops;
	}
	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->Inventory = new Enum("Inventory", []);
		$this->namedtag->Inventory->setTagType(NBT::TAG_Compound);
		if($this->inventory !== null){
			for($slot = 0; $slot < 9; ++$slot){
				$hotbarSlot = $this->inventory->getHotbarSlotIndex($slot);
				if($hotbarSlot !== -1){
					$item = $this->inventory->getItem($hotbarSlot);
					if($item->getId() !== 0 and $item->getCount() > 0){
						$tag = NBT::putItemHelper($item, $slot);
						$tag->TrueSlot = new Byte("TrueSlot", $hotbarSlot);
						$this->namedtag->Inventory[$slot] = $tag;
						continue;
					}
				}
				$this->namedtag->Inventory[$slot] = new Compound("", [
					new Byte("Count", 0),
					new Short("Damage", 0),
					new Byte("Slot", $slot),
					new Byte("TrueSlot", -1),
					new Short("id", 0),
				]);
			}
			$slotCount = $this->getLevel()->getServer()->inventoryNum + 9;
			for($slot = 9; $slot < $slotCount; ++$slot){
				$item = $this->inventory->getItem($slot - 9);
				$this->namedtag->Inventory[$slot] = NBT::putItemHelper($item, $slot);
			}
			for($slot = 100; $slot < 104; ++$slot){
				$item = $this->inventory->getItem($this->inventory->getSize() + $slot - 100);
				if($item instanceof ItemItem and $item->getId() !== ItemItem::AIR){
					$this->namedtag->Inventory[$slot] = NBT::putItemHelper($item, $slot);
				}
			}
		}
		if(strlen($this->getSkinData()) > 0){
			$this->namedtag->Skin = new Compound("Skin", [
				"Data" => new String("Data", $this->getSkinData()),
				"Name" => new String("Name", $this->getSkinName()),
			]);
		}
	}
	public function spawnTo(Player $player){
		if($player !== $this and !isset($this->hasSpawned[$player->getLoaderId()])){
			$this->hasSpawned[$player->getLoaderId()] = $player;
			if(strlen($this->skin) < 64 * 32 * 4){
				throw new \InvalidStateException((new \ReflectionClass($this))->getShortName() . " must have a valid skin set");
			}
			if(!($this instanceof Player)){
				$this->server->updatePlayerListData($this->getUniqueId(), $this->getId(), $this->getName(), $this->skinName, $this->skin, [$player]);
			}
			$pk = new AddPlayerPacket();
			$pk->uuid = $this->getUniqueId();
			$pk->username = $this->getName();
			$pk->eid = $this->getId();
			$pk->x = $this->x;
			$pk->y = $this->y;
			$pk->z = $this->z;
			$pk->speedX = $this->motionX;
			$pk->speedY = $this->motionY;
			$pk->speedZ = $this->motionZ;
			$pk->yaw = $this->yaw;
			$pk->pitch = $this->pitch;
			$pk->item = $this->getInventory()->getItemInHand();
			$pk->metadata = $this->dataProperties;
			$player->dataPacket($pk);
			$this->sendLinkedData();
			$this->inventory->sendArmorContents($player);
			if(!($this instanceof Player)){
				$this->server->removePlayerListData($this->getUniqueId(), [$player]);
			}
		}
	}
	public function despawnFrom(Player $player){
		if(isset($this->hasSpawned[$player->getLoaderId()])){
			$pk = new RemovePlayerPacket();
			$pk->eid = $this->getId();
			$pk->clientId = $this->getUniqueId();
			$player->dataPacket($pk);
			unset($this->hasSpawned[$player->getLoaderId()]);
		}
	}
	public function close(){
		if(!$this->closed){
			if(!($this instanceof Player) or $this->loggedIn){
				foreach($this->inventory->getViewers() as $viewer){
					$viewer->removeWindow($this->inventory);
				}
			}
			parent::close();
		}
	}
}