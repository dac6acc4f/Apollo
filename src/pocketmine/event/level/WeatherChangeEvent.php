<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://mcper.cn
 *
 */

namespace pocketmine\event\level;

use pocketmine\event\Cancellable;
use pocketmine\level\Level;
use pocketmine\level\weather\Weather;

class WeatherChangeEvent extends LevelEvent implements Cancellable{
	public static $handlerList = null;

	private $weather;
	private $duration;

	public function __construct(Level $level,$weather,$duration){
		parent::__construct($level);
		$this->weather = (int)$weather;
		$this->duration = (int)$duration;
	}

	public function getWeather(){
		return (int)$this->weather;
	}

	public function setWeather($weather = Weather::SUNNY){
		$this->weather = (int)$weather;
	}

	public function getDuration(){
		return (int) $this->duration;
	}

	public function setDuration($duration){
		$this->duration = (int)$duration;
	}

}