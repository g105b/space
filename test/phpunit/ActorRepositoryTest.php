<?php
namespace Space\Test;
use Gt\Session\SessionStore;
use Gt\Ulid\Ulid;
use PHPUnit\Framework\TestCase;
use Space\Actor\ActorRepository;
use Space\Actor\Location\Cell;
use Space\Actor\Player;
use Space\Test\Helper\MockCaller;

class ActorRepositoryTest extends TestCase {
	private string $baseTestDir = "/tmp/exogenesis/test";

	public function tearDown():void {
		exec("rm -rf $this->baseTestDir");
	}

	public function testGetPlayer_noSession():void {
		$sut = new ActorRepository(self::createMock(SessionStore::class));
		$player = $sut->getPlayer();
		self::assertNull($player);
	}

	public function testGetPlayer():void {
		$session = self::createMock(SessionStore::class);
		$sut = new ActorRepository($session);
		$player = new Player(
			new Ulid("player"),
			self::createMock(Cell::class),
		);

		$session->expects(self::once())
			->method("getInstance")
			->with(ActorRepository::SESSION_KEY_PLAYER, Player::class)
			->willReturn($player);

		self::assertSame($player, $sut->getPlayer());
	}

	public function testCreatePlayer():void {
		$session = self::createMock(SessionStore::class);
		$sut = new ActorRepository($session);
		$player = new Player(
			new Ulid("player"),
			self::createMock(Cell::class),
		);

		$session->expects(self::once())
			->method("set")
			->with(ActorRepository::SESSION_KEY_PLAYER, $player);
		$sut->createPlayer($player);
	}

	public function testSave():void {

		$dataDir = "$this->baseTestDir/actor";
		$playerId = new Ulid("player");
		$expectedIni = "TEST";
		$fileWriteFunction = self::createMock(MockCaller::class);
		$fileWriteFunction->expects(self::once())
			->method("__invoke")
			->with("$dataDir/$playerId.ini", $expectedIni);

		$session = self::createMock(SessionStore::class);
		$sut = new ActorRepository($session, dataPath: $dataDir, fileWriteFunction: $fileWriteFunction);

		$player = self::createMock(Player::class);
		$player->id = $playerId;
		$player->expects(self::once())
			->method("iniSerialize")
			->willReturn($expectedIni);
		$sut->save($player);

		self::assertDirectoryExists($dataDir);
	}
}
