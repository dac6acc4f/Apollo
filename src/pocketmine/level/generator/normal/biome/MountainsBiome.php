<?php
namespace pocketmine\level\generator\normal\biome;
class MountainsBiome extends GrassyBiome{
	public function __construct(){
		parent::__construct();
		$this->setElevation(56, 127);
	}
	public function getName(){
		return "Mountains";
	}
}
