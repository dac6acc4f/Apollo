<?php
namespace pocketmine\level\generator\normal\biome;
use pocketmine\block\Block;
use pocketmine\block\Flower as FlowerBlock;
use pocketmine\level\generator\populator\Flower;
use pocketmine\level\generator\populator\LilyPad;
use pocketmine\level\generator\Populator\Tree;
class SwampBiome extends GrassyBiome{
	public function __construct(){
		parent::__construct();
		$flower = new Flower();
		$flower->setBaseAmount(2);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_BLUE_ORCHID]);
		$this->addPopulator($flower);
		$lilypad = new LilyPad();
		$lilypad->setBaseAmount(4);
		$this->addPopulator($lilypad);
		$trees = new Tree();
		$trees->setBaseAmount(2);
		$this->addPopulator($trees);
		$this->setElevation(61, 68);
		$this->temperature = 0.8;
		$this->rainfall = 0.9;
	}
	public function getName(){
		return "Swamp";
	}
	public function getColor(){
		return 0x6a7039;
	}
}
