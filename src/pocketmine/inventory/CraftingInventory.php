<?php
namespace pocketmine\inventory;
class CraftingInventory extends BaseInventory{

	private $resultInventory;

	public function __construct(InventoryHolder $holder, Inventory $resultInventory, InventoryType $inventoryType){
		if($inventoryType->getDefaultTitle() !== "Crafting"){
			throw new \InvalidStateException("Invalid Inventory type, expected CRAFTING or WORKBENCH");
		}
		$this->resultInventory = $resultInventory;
		parent::__construct($holder, $inventoryType);
	}

	public function getResultInventory(){
		return $this->resultInventory;
	}

	public function getSize(){
		return $this->getResultInventory()->getSize() + parent::getSize();
	}
}