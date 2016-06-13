<?php
namespace pocketmine\inventory;
use pocketmine\item\Item;
class BaseTransaction implements Transaction{
	protected $inventory;
	protected $slot;
	protected $sourceItem;
	protected $targetItem;
	protected $creationTime;
	public function __construct(Inventory $inventory, $slot, Item $sourceItem, Item $targetItem){
		$this->inventory = $inventory;
		$this->slot = (int) $slot;
		$this->sourceItem = clone $sourceItem;
		$this->targetItem = clone $targetItem;
		$this->creationTime = microtime(true);
	}
	public function getCreationTime(){
		return $this->creationTime;
	}
	public function getInventory(){
		return $this->inventory;
	}
	public function getSlot(){
		return $this->slot;
	}
	public function getSourceItem(){
		return clone $this->sourceItem;
	}
	public function getTargetItem(){
		return clone $this->targetItem;
	}
}
