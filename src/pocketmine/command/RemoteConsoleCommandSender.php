<?php
namespace pocketmine\command;

use pocketmine\event\TextContainer;

class RemoteConsoleCommandSender extends ConsoleCommandSender{

	private $messages = "";

	public function sendMessage($message){
		if($message instanceof TextContainer){
			$message = $this->getServer()->getLanguage()->translate($message);
		}else{
			$message = $this->getServer()->getLanguage()->translateString($message);
		}

		$this->messages .= trim($message, "\r\n") . "\n";
	}

	public function getMessage(){
		return $this->messages;
	}

	public function getName(){
		return "Rcon";
	}


}
