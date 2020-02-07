<?php

/*
 * Basically... It works
 */

function br2nl( $input ) {
	return preg_replace('/<br(\s+)?\/?>/i', "\n", $input);
}

function fetchToots(string $hashtag) {
	$apiBaseUrl = "https://chaos.social/api/v1/timelines/tag/" . $hashtag;

	$page = 0;
	$lastId = 0;
	$continue = true;;
	$allToots = [];

	$apiBaseUrl .= '?limit=40';

	do {
		$apiUrl = $apiBaseUrl;
		if ($page > 0) {
			$apiBaseUrl .= '&max_id=' . ($lastId - 1);
		}
		$ch = curl_init($apiBaseUrl . '?limit=40');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($ch);

		$toots = json_decode($json, true);

		if (count($toots) < 40) {
			$continue = false;
		}
		if (count($toots) > 0) {
			$lastToot = $toots[count($toots) - 1];
		}
		$lastId = (int) $lastToot['id'];

		$page++;
		sleep(2);
		$allToots = array_merge($allToots, $toots);
	} while ($continue);

	return $allToots;
}

#$apiBaseUrl = "https://chaos.social/api/v1/timelines/tag/Partyline42";
$apiBaseUrl = "https://chaos.social/api/v1/timelines/tag/36c3";

$toots = fetchToots('Partyline42');

foreach ($toots as $key => $toot) {
	$account = $toot['account'];
	$fullAccName = $account['acct'];
	if (strpos($fullAccName, '@') === False) {
		$fullAccName .= '@chaos.social';
	}

	echo "-----\n" . $account['display_name'] . ' (' . $fullAccName . '): ' . strip_tags(br2nl($toot['content']))  .  "\n";
}
