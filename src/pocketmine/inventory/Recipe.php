<?php
namespace pocketmine\inventory;
use pocketmine\utils\UUID;
interface Recipe{

	public function getResult();
	public function registerToCraftingManager();
	public function getId();
}