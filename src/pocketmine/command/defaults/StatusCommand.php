<?php
namespace pocketmine\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
class StatusCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.status.description",
			"%pocketmine.command.status.usage"
		);
		$this->setPermission("pocketmine.command.status");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		$mUsage = Utils::getMemoryUsage(true);
		$rUsage = Utils::getRealMemoryUsage();

		$server = $sender->getServer();
		$sender->sendMessage(TextFormat::GREEN . "---- " . TextFormat::WHITE . "%pocketmine.command.status.title" . TextFormat::GREEN . " ----");
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.player" . TextFormat::GREEN ." ". \count($sender->getServer()->getOnlinePlayers()) . "/" . $sender->getServer()->getMaxPlayers());

		$time = microtime(true) - \pocketmine\START_TIME;

		$seconds = floor($time % 60);
		$minutes = null;
		$hours = null;
		$days = null;

		if($time >= 60){
			$minutes = floor(($time % 3600) / 60);
			if($time >= 3600){
				$hours = floor(($time % (3600 * 24)) / 3600);
				if($time >= 3600 * 24){
					$days = floor($time / (3600 * 24));
				}
			}
		}

		$uptime = ($minutes !== null ?
				($hours !== null ?
					($days !== null ?
						"$days %pocketmine.command.status.days "
						: "") . "$hours %pocketmine.command.status.hours "
					: "") . "$minutes %pocketmine.command.status.minutes "
				: "") . "$seconds %pocketmine.command.status.seconds";

		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.uptime " . TextFormat::RED . $uptime);

		$tpsColor = TextFormat::GREEN;
		if($server->getTicksPerSecondAverage() < 10){
			$tpsColor = TextFormat::GOLD;
		}elseif($server->getTicksPerSecondAverage() < 1){
			$tpsColor = TextFormat::RED;
		}

		$tpsColour = TextFormat::GREEN;
		if($server->getTicksPerSecond() < 17){
			$tpsColour = TextFormat::GOLD;
		}elseif($server->getTicksPerSecond() < 12){
			$tpsColour = TextFormat::RED;
		}

		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.AverageTPS " . $tpsColor . $server->getTicksPerSecondAverage() . " (" . $server->getTickUsageAverage() . "%)");
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.CurrentTPS " . $tpsColour . $server->getTicksPerSecond() . " (" . $server->getTickUsage() . "%)");

		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.Networkupload " . TextFormat::GREEN . \round($server->getNetwork()->getUpload() / 1024, 2) . " kB/s");
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.Networkdownload " . TextFormat::GREEN . \round($server->getNetwork()->getDownload() / 1024, 2) . " kB/s");

		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.Threadcount " . TextFormat::GREEN . Utils::getThreadCount());

		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.Mainmemory " . TextFormat::GREEN . number_format(round(($mUsage[0] / 1024) / 1024, 2)) . " MB.");
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.Totalmemory " . TextFormat::GREEN . number_format(round(($mUsage[1] / 1024) / 1024, 2)) . " MB.");
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.Totalvirtualmemory " . TextFormat::GREEN . number_format(round(($mUsage[2] / 1024) / 1024, 2)) . " MB.");
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.Heapmemory " . TextFormat::GREEN . number_format(round(($rUsage[0] / 1024) / 1024, 2)) . " MB.");
		$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.Maxmemorysystem " . TextFormat::WHITE . number_format(round(($mUsage[2] / 1024) / 1024, 2)) . " MB.");

		if($server->getProperty("memory.global-limit") > 0){
			$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.Maxmemorymanager " . TextFormat::GREEN . number_format(round($server->getProperty("memory.global-limit"), 2)) . " MB.");
		}
		foreach($server->getLevels() as $level){
			$sender->sendMessage(TextFormat::GOLD . "%pocketmine.command.status.World \"" . $level->getFolderName() . "\"" . ($level->getFolderName() !== $level->getName() ? " (" . $level->getName() . ")" : "") . ": " .
				TextFormat::RED . number_format(count($level->getChunks())) . TextFormat::GREEN . " %pocketmine.command.status.chunks " .
				TextFormat::RED . number_format(count($level->getEntities())) . TextFormat::GREEN . " %pocketmine.command.status.entities " .
				TextFormat::RED . number_format(count($level->getTiles())) . TextFormat::GREEN . " %pocketmine.command.status.tiles " .
				"%pocketmine.command.status.Time " . (($level->getTickRate() > 1 or $level->getTickRateTime() > 40) ? TextFormat::RED : TextFormat::YELLOW) . round($level->getTickRateTime(), 2) . "%pocketmine.command.status.ms" . ($level->getTickRate() > 1 ? " (tick rate " . $level->getTickRate() . ")" : "")
			);
		}

		return true;
	}
}
