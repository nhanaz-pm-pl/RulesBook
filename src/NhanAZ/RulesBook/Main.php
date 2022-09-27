<?php

declare(strict_types=1);

namespace NhanAZ\RulesBook;

use cooldogedev\libBook\LibBook;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		LibBook::register($this);
	}

	private function sendPreview(Player $player): void {
		$item = VanillaItems::WRITTEN_BOOK();
		foreach ($this->getConfig()->get("pages") as $pageId => $pageText) {
			$page = explode("_", $pageId)[1] - 1;
			$item->setPageText((int) $page, TextFormat::colorize($pageText));
		}
		LibBook::sendPreview($player, $item);
	}

	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		if ($player->hasPlayedBefore()) {
			return;
		}
		$this->sendPreview($player);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if ($command->getName() === "rulesbook") {
			if (!$sender instanceof Player) {
				$sender->sendMessage(TextFormat::RED . "You can't use this command in the terminal!");
				return true;
			}
			$this->sendPreview($sender);
			return true;
		}
		return false;
	}
}
