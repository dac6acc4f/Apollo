<?php
namespace pocketmine\inventory;
use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\utils\UUID;
class FurnaceRecipe implements Recipe{
	private $id = null;
	private $output;
	private $ingredient;

	public function __construct(Item $result, Item $ingredient){
		$this->output = clone $result;
		$this->ingredient = clone $ingredient;
	}

	public function getId(){
		return $this->id;
	}

	public function setId(UUID $id){
		if($this->id !== null){
			throw new \InvalidStateException("Id is already set");
		}

		$this->id = $id;
	}

	public function setInput(Item $item){
		$this->ingredient = clone $item;
	}

	public function getInput(){
		return clone $this->ingredient;
	}

	public function getResult(){
		return clone $this->output;
	}

	public function registerToCraftingManager(){
		Server::getInstance()->getCraftingManager()->registerFurnaceRecipe($this);
	}
}