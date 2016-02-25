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

function curl_get_contents($url)
{
  $ch = curl_init($url);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
?>