<?php
namespace pocketmine\level\generator\normal\biome;
use pocketmine\block\Block;
class IcePlainsBiome extends GrassyBiome{
	public function __construct(){
		parent::__construct();
		$this->setGroundCover([
			Block::get(Block::SNOW_LAYER, 0),
			Block::get(Block::GRASS, 0),
			Block::get(Block::DIRT, 0),
			Block::get(Block::DIRT, 0),
			Block::get(Block::DIRT, 0),
		]);
		$this->addPopulator($tallGrass);
		$this->setElevation(56, 74);
		$this->temperature = 0.05;
		$this->rainfall = 0.8;
	}
	public function getName(){
		return "Ice Plains";
	}
}
