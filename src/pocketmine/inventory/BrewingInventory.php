<?php
namespace pocketmine\inventory;
use pocketmine\item\Item;
use pocketmine\tile\BrewingStand;
class BrewingInventory extends ContainerInventory{
	public function __construct(BrewingStand $tile){
		parent::__construct($tile, InventoryType::get(InventoryType::BREWING_STAND));
	}

	public function getHolder(){
		return $this->holder;
	}

	public function setIngredient(Item $item){
		$this->setItem(0, $item);
	}

	public function getIngredient(){
		return $this->getItem(0);
	}

	public function onSlotChange($index, $before){
		parent::onSlotChange($index, $before);

		$this->getHolder()->scheduleUpdate();
	}
}