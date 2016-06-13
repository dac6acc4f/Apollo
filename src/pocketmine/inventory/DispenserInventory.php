<?php
namespace pocketmine\inventory;
use pocketmine\tile\Dispenser;
class DispenserInventory extends ContainerInventory{
	public function __construct(Dispenser $tile){
		parent::__construct($tile, InventoryType::get(InventoryType::DISPENSER));
	}

	public function getHolder(){
		return $this->holder;
	}
}