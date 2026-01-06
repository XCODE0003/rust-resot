<?php
namespace App\Lib\GameServer;

interface GameServerInterface {

    public static function all_online_count($server_id);

    public static function online_count($server_id);

    public static function createGameAccount($account, $password);

    public static function getCharacter($char_id);

    public static function getCharacterByName($char_name);

    public static function getCharacters($accounts);

    public static function checkNameCharacter($nickname);

    public static function charactersCount($accounts);

    public static function teleportCharacterMainTown($char_id, $town_cord);

    public static function transferItemWarehouse($char_id, $item_id, $amount, $inventory);

    public static function transferItemGameServer($char_id, $character, $amount, $warehouse);

    public static function transferDonateGameServer($char_id, $character, $amount);

    public static function changeNameCharacter($char_id, $nickname);

    public static function changeColorCharacter($char_id, $type, $color);

    public static function searchGameAccountByCharacter($character);

    public static function getAccountHWID($account_name);

    public static function getPlayersOnline($server);

    public static function setServerConfig($server);
}
