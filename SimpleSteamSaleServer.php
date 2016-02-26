<?php
header("Access-Control-Allow-Origin: *");
if (isset($_REQUEST['action'])) {
	session_start();
    switch ($_REQUEST['action']) {
		case 'getSteamAppIDs':
            getSteamAppIDs();
            break;
		case 'getSteamSaleInformation':
			getSteamSaleInformation($_REQUEST['appid']);
			break;
		case 'getCurrentData':
			getCurrentData();
			break;
		case 'putData':
			putData($_REQUEST['data']);
			break;
		case 'buildSteamSaleInformation':
			echo "here";
			buildSteamSaleInformation();
			break;
    }
}

function getSteamAppIDs() {
	$result = 'https://api.steampowered.com/ISteamApps/GetAppList/v2';
	$d = curl_get_contents($result);
	echo $d;
}

function getSteamSaleInformation($appid) {
	$result = 'http://store.steampowered.com/api/appdetails?appids=' . $appid . '&filters=price_overview';
	$d = curl_get_contents($result);
	echo $d;
}

function getCurrentData() {
	$result = 'http://localhost:8081/SteamSaleWatcherServer/gameList.json';
	$d = curl_get_contents($result);
	echo $d;
}

function putData($data) {
	file_put_contents('gameList.json', $data);
}

function buildSteamSaleInformation() {
	echo "hi";
	// Get all the games
	$result = 'https://api.steampowered.com/ISteamApps/GetAppList/v2';
	$d = curl_get_contents($result);
	$appIDs = json_decode($d, true);
	$array = $appIDs["applist"]["apps"];
	
	$steaminfo = array();
	
	foreach ($array as $map) {
		// Get the data for each game
		try {
			$result = 'http://store.steampowered.com/api/appdetails?appids=' . $map["appid"] . '&filters=price_overview';
			$d = curl_get_contents($result);
			$data = json_decode($d, true);
			var_dump($data);
			// Make sure the data and keys are fine
			if ($data != null 
			&& array_key_exists ( $map["appid"] , $data) 
			&& array_key_exists ( "data" , $data[$map["appid"]])
			&& array_key_exists ( "price_overview" , $data[$map["appid"]]["data"])) {
				$discountPrice = $data[$map["appid"]]["data"]["price_overview"]["discount_percent"];
				$steaminfo[$map["name"]] = $discountPrice;
			}
		} catch (Exception $e) {
			
		}
	}
		
	putData(json_encode($steaminfo));
	echo "Compled this ish";
	echo true;
}

function curl_get_contents($url)
{
  set_time_limit(0);
  ini_set("max_execution_time", 0);
  $ch = curl_init($url);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  try {
	  $data = curl_exec($ch);
	  curl_close($ch);
	  return $data;
  } catch (Exception $e) {
	  
  }
  return null;
}
?>