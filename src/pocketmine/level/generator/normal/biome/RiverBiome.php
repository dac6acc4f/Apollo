<?php
namespace pocketmine\level\generator\normal\biome;
use pocketmine\level\generator\populator\Sugarcane;
use pocketmine\level\generator\populator\TallGrass;
use pocketmine\level\generator\populator\TallSugarcane;
use pocketmine\block\Sapling;
use pocketmine\level\generator\populator\Tree;
class RiverBiome extends GrassyBiome{
	public function __construct(){
		parent::__construct();
		$sugarcane = new Sugarcane();
		$sugarcane->setBaseAmount(6);
		$tallSugarcane = new TallSugarcane();
		$tallSugarcane->setBaseAmount(60);
		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(5);
		$trees = new Tree(Sapling::SPRUCE);
		$trees->setBaseAmount(1);
		$this->addPopulator($sugarcane);
		$this->addPopulator($tallSugarcane);
		$this->addPopulator($tallGrass);
                $this->addPopulator($trees);
		$this->setElevation(56, 74);
		$this->temperature = 0.5;
		$this->rainfall = 0.7;
	}
	public function getName(){
		return "River";
	}
}
