<?php
namespace pocketmine\block;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Int;
use pocketmine\nbt\tag\String;
use pocketmine\Player;
use pocketmine\tile\Dispenser as TileDispenser;
use pocketmine\tile\Tile;
class Dispenser extends Solid{

	protected $id = self::DISPENSER;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function canBeActivated(){
		return true;
	}

	public function getHardness() {
		return 3.5;
	}

	public function getName(){
		return "Dispenser";
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$dispenser = null;
		if($player instanceof Player){
			$pitch = $player->getPitch();
			if(abs($pitch) >= 45){
				if($pitch < 0) $f = 4;
				else $f = 5;
			} else $f = $player->getDirection();
		} else $f = 0;
		$faces = [
			3 => 3,
			0 => 4,
			2 => 5,
			1 => 2,
			4 => 0,
			5 => 1
		];
		$this->meta = $faces[$f];

		$this->getLevel()->setBlock($block, $this, true, true);
		$nbt = new Compound("", [
			new Enum("Items", []),
			new String("id", Tile::DISPENSER),
			new Int("x", $this->x),
			new Int("y", $this->y),
			new Int("z", $this->z)
		]);
		$nbt->Items->setTagType(NBT::TAG_Compound);

		if($item->hasCustomName()){
			$nbt->CustomName = new String("CustomName", $item->getCustomName());
		}

		if($item->hasCustomBlockData()){
			foreach($item->getCustomBlockData() as $key => $v){
				$nbt->{$key} = $v;
			}
		}

		Tile::createTile(Tile::DISPENSER, $this->getLevel()->getChunk($this->x >> 4, $this->z >> 4), $nbt);

		return true;
	}

	public function activate(){
		$tile = $this->getLevel()->getTile($this);
		if($tile instanceof TileDispenser){
			$tile->activate();
		}
	}

	public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			$t = $this->getLevel()->getTile($this);
			$dispenser = null;
			if($t instanceof TileDispenser){
				$dispenser = $t;
			}else{
				$nbt = new Compound("", [
					new Enum("Items", []),
					new String("id", Tile::DISPENSER),
					new Int("x", $this->x),
					new Int("y", $this->y),
					new Int("z", $this->z)
				]);
				$nbt->Items->setTagType(NBT::TAG_Compound);
				$dispenser = Tile::createTile(Tile::DISPENSER, $this->getLevel()->getChunk($this->x >> 4, $this->z >> 4), $nbt);
			}

			if($player->isCreative() and $player->getServer()->limitedCreative){
				return true;
			}

			$player->addWindow($dispenser->getInventory());
		}

		return true;
	}

	public function getDrops(Item $item){
		return [
			[$this->id, 0, 1],
		];
	}
}
