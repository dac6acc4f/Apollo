<?php
namespace pocketmine\level\generator\normal\biome;
use pocketmine\block\Block;
class MesaBiome extends DesertBiome{
	public function __construct(){
		parent::__construct();
		$this->setElevation(56, 74);
		$this->temperature = 2.0;
		$this->rainfall = 0.5;
		$this->setGroundCover([
			Block::get(Block::HARDENED_CLAY, 0),
			Block::get(Block::STAINED_HARDENED_CLAY, 0),
			Block::get(Block::STAINED_HARDENED_CLAY, 1),
			Block::get(Block::STAINED_HARDENED_CLAY, 2),
			Block::get(Block::STAINED_HARDENED_CLAY, 3),
			Block::get(Block::STAINED_HARDENED_CLAY, 4),
			Block::get(Block::STAINED_HARDENED_CLAY, 5),
			Block::get(Block::STAINED_HARDENED_CLAY, 6),
			Block::get(Block::STAINED_HARDENED_CLAY, 7),
			Block::get(Block::STAINED_HARDENED_CLAY, 8),
			Block::get(Block::STAINED_HARDENED_CLAY, 9),
			Block::get(Block::STAINED_HARDENED_CLAY, 10),
			Block::get(Block::STAINED_HARDENED_CLAY, 11),
			Block::get(Block::STAINED_HARDENED_CLAY, 12),
			Block::get(Block::STAINED_HARDENED_CLAY, 13),
			Block::get(Block::STAINED_HARDENED_CLAY, 14),
			Block::get(Block::STAINED_HARDENED_CLAY, 15),
		]);
	}
	public function getName(){
		return "Mesa";
	}
}
