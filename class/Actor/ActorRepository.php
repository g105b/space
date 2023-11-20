<?php
namespace Space\Actor;

use Gt\Session\SessionStore;

class ActorRepository {
	const SESSION_KEY_PLAYER = "player";

	/** @var callable */
	private $fileWriteFunction;

	public function __construct(
		private SessionStore $session,
		private string $dataPath = "data/actor",
		?callable $fileWriteFunction = null,
	) {
		$this->fileWriteFunction = $fileWriteFunction ?? file_put_contents(...);
	}

	public function getPlayer():?Player {
		return $this->session->getInstance(self::SESSION_KEY_PLAYER, Player::class);
	}

	public function createPlayer(Player $player):void {
		$this->session->set(self::SESSION_KEY_PLAYER, $player);
	}

	public function save(Actor $actor):void {
		if(!is_dir($this->dataPath)) {
			mkdir($this->dataPath, recursive: true);
		}

		call_user_func($this->fileWriteFunction,
			"$this->dataPath/$actor->id.ini",
			$actor->iniSerialize(),
		);
	}
}
