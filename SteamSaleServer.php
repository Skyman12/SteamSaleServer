<?php
header("Access-Control-Allow-Origin: *");
if (isset($_REQUEST['action'])) {
	session_start();
    switch ($_REQUEST['action']) {
		case 'getCurrentData':
			getCurrentData();
			break;
		case 'buildSteamSaleInformation':
			buildSteamSaleInformation();
			break;
		case 'addUser':
			addUser($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['email']);
			break;
		case 'getAllUsers':
			getAllUsers();
			break;
		case 'addGameToUser':
			addGameToUser($_REQUEST['username'], $_REQUEST['gameName'], $_REQUEST['discountAmount']);
			break;
		case 'removeGameFromUser':
			removeGameFromUser($_REQUEST['username'], $_REQUEST['gameName']);
			break;
		case 'getUser':
			getUser($_REQUEST['username']);
			break;
    }
}

function getCurrentData() {
	$result = 'http://localhost:8081/SteamSaleServer/gameList.json';
	$d = curl_get_contents($result);
	echo $d;
}

function putData($data) {
	file_put_contents('gameList.json', $data);
}

function buildSteamSaleInformation() {
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
				$steaminfo[$map["name"]] = $data[$map["appid"]]["data"]["price_overview"];
			}
		} catch (Exception $e) {
			
		}
	}
		
	putData(json_encode($steaminfo));
}

function addUser($username, $password, $email) {
	$inp = file_get_contents('users.json');
	$tempArray = json_decode($inp, true);
	
	if ($tempArray == null || !array_key_exists($username, $tempArray))
	{
		$gameData = array();
		$data = array('username' => $username, 'password' => $password,
					  'email' => $email, 'gameList' => $gameData);
		
		var_dump($data);
		$tempArray[$username] = $data;
		$jsonData = json_encode($tempArray);
		file_put_contents('users.json', $jsonData);
		echo true;
	}
	echo false;
}

function getAllUsers() {
	$inp = file_get_contents('users.json');
	$tempArray = json_decode($inp, true);
	echo json_encode($tempArray);
}

function getUser($username) {
	$inp = file_get_contents('users.json');
	$tempArray = json_decode($inp, true);
	echo json_encode($tempArray[$username]);
}

function addGameToUser($username, $gameName, $discountAmount) {
	$inp = file_get_contents('users.json');
	$tempArray = json_decode($inp, true);
	if (array_key_exists($username, $tempArray)) {
		$tempArray[$username]["gameList"][$gameName] = $discountAmount;
		$jsonData = json_encode($tempArray);
		file_put_contents('users.json', $jsonData);
	}
}

function removeGameFromUser($username, $gameName) {
	$inp = file_get_contents('users.json');
	$tempArray = json_decode($inp, true);
	if (array_key_exists($username, $tempArray)) {
		if (array_key_exists($gameName, $tempArray[$username]["gameList"])) {
			unset($tempArray[$username]["gameList"][$gameName]);
			$jsonData = json_encode($tempArray);
			file_put_contents('users.json', $jsonData);
		}
	}
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