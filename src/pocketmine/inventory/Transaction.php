<?php
namespace pocketmine\inventory;
use pocketmine\item\Item;
interface Transaction{
	public function getInventory();
	public function getSlot();
	public function getSourceItem();
	public function getTargetItem();
	public function getCreationTime();
}