<?php
namespace pocketmine\inventory;
use pocketmine\item\Item;
use pocketmine\tile\Furnace;
class FurnaceInventory extends ContainerInventory{
	public function __construct(Furnace $tile){
		parent::__construct($tile, InventoryType::get(InventoryType::FURNACE));
	}

	public function getHolder(){
		return $this->holder;
	}

	public function getResult(){
		return $this->getItem(2);
	}

	public function getFuel(){
		return $this->getItem(1);
	}

	public function getSmelting(){
		return $this->getItem(0);
	}

	public function setResult(Item $item){
		return $this->setItem(2, $item);
	}

	public function setFuel(Item $item){
		return $this->setItem(1, $item);
	}

	public function setSmelting(Item $item){
		return $this->setItem(0, $item);
	}

	public function onSlotChange($index, $before){
		parent::onSlotChange($index, $before);

		$this->getHolder()->scheduleUpdate();
	}
}
