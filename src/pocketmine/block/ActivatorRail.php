<?php

namespace pocketmine\block;

class ActivatorRail extends PoweredRail {

    protected $id = self::ACTIVATOR_RAIL;

    public function __construct($meta = 0){
        $this->meta = $meta;
    }

    public function getName() {
        return "Activator Rail";
    }
}
