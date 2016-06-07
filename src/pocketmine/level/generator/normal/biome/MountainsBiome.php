<?php
namespace pocketmine\level\generator\normal\biome;
class MountainsBiome extends GrassyBiome{
	public function __construct(){
		parent::__construct();
		$this->setElevation(63, 127);
	}
	public function getName(){
		return "Small Mountains";
	}
}
