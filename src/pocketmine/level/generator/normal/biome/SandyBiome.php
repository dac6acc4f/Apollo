<?php
namespace pocketmine\level\generator\normal\biome;
use pocketmine\block\Block;
use pocketmine\level\generator\populator\Cactus;
use pocketmine\level\generator\populator\TallCacti;
use pocketmine\level\generator\populator\DeadBush;
class SandyBiome extends GrassyBiome{
	public function __construct(){
		parent::__construct();
		$cactus = new Cactus();
		$cactus->setBaseAmount(2);
		$tallCacti = new TallCacti();
		$tallCacti->setBaseAmount(2);
		$deadBush = new DeadBush();
		$deadBush->setBaseAmount(2);
		$this->addPopulator($cactus);
		$this->addPopulator($tallCacti);
		$this->addPopulator($deadBush);
		$this->temperature = 0.05;
		$this->rainfall = 0.8;
		$this->setGroundCover([
			Block::get(Block::SAND, 0),
			Block::get(Block::SAND, 0),
			Block::get(Block::SAND, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
			Block::get(Block::SANDSTONE, 0),
		]);
	}
	public function getName() {
		return "Sandy";
	}

	public function getColor(){
		return $this->grassColor;
	}
}
