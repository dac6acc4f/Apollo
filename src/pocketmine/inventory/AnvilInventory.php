<?php
namespace pocketmine\inventory;
use pocketmine\level\Position;
use pocketmine\Player;
class AnvilInventory extends ContainerInventory{
	public function __construct(Position $pos){
		parent::__construct(new FakeBlockMenu($this, $pos), InventoryType::get(InventoryType::ANVIL));
	}

	public function getHolder(){
		return $this->holder;
	}

	public function hasSource(){
		if($this->getItem(0)->getId() != 0 or $this->getItem(1)->getId() != 0) return true;
		return false;
	}

	public function onClose(Player $who){
		parent::onClose($who);
		
		$this->getHolder()->getLevel()->dropItem($this->getHolder()->add(0.5, 0.5, 0.5), $this->getItem(1));
		$this->getHolder()->getLevel()->dropItem($this->getHolder()->add(0.5, 0.5, 0.5), $this->getItem(0));
		$this->clear(0);
		$this->clear(1);
		$this->clear(2);
	}
}
