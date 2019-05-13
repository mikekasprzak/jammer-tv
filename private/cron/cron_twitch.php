<?php
namespace twitch;

require_once __DIR__."/../../common/lib/querystring.php";
require_once __DIR__."/../../common/lib/fetch_twitch.php";

// Lookup by Id: https://api.twitch.tv/helix/users?id=69760921
//const USER_IDS = [
//	"69760921", // LudumDare
//];

// Lookup by Name: https://api.twitch.tv/helix/games?name=Under%20Development
// Lookup by Id:   https://api.twitch.tv/helix/games?id=509670
const GAME_IDS = [
//	"509670",	// Science & Technology
//	"509660",	// Art
//	"509673",	// Makers & Crafting
	"509663",	// Special Events
//	"26936",	// Music & Performing Arts
//	"509667",	// Food & Drink
//	"509658",	// Just Chatting
//	"509868",	// Under Development
//	"0",		// no game set
];

// View Tags:    https://www.twitch.tv/directory/all/tags/c8d6d6ee-3b02-4ae7-81e9-4c0f434941cc
// Lookup by Id: https://api.twitch.tv/helix/tags/streams?tag_id=c8d6d6ee-3b02-4ae7-81e9-4c0f434941cc
const TAG_IDS = [
	"c8d6d6ee-3b02-4ae7-81e9-4c0f434941cc",	// Ludum Dare
	"c48e0bf4-7a1b-4ffd-893c-12e46e664f7f", // Game Jam (many folks incorrectly use this)

	"a59f1e4e-257b-4bd0-90c7-189c3efbf917",	// Programming
	"f588bd74-e496-4d11-9169-3597f38a5d25",	// Game Development
	"6e23d976-33ec-47e8-b22b-3727acd41862",	// Mobile Development
	"6f86127d-6051-4a38-94bb-f7b475dde109",	// Software Development
	"c23ce252-cf78-4b98-8c11-8769801aaf3a",	// Web Development

	"e36d0169-268a-4c62-a4f4-ddf61a0b3ae4", // Creative (implied)
	"2610cff9-10ae-4cb3-8500-778e6722fbb5", // IRL (implied)

	"d72d9de6-1df8-4c4e-b6a2-74e6f4c80557",	// Indie Game
	"e027fb8b-219e-4959-8240-a4a082be0316",	// Retro
	"7cefbf30-4c3e-4aa7-99cd-70aabb662f27", // Speedrun

	"b97ee881-e15a-455d-9876-657fcba7cfd8", // 3D Modeling
	"02ba4017-ed3b-4b82-ab20-011860784f77", // Digital Art
	"3c0a4e1f-6863-4dad-bb4e-538326306bef", // Pixel Art
	"5ec52c4f-a055-404c-82fe-ea98c74c7fe6", // Traditional Art
	"f0ab2b07-14ed-4429-8ea3-3d7d400a50cd", // Vector Art

	"ddb625af-5920-49cc-9f13-3716f87941dc", // Music Production

	"7b49f69a-5d95-4c94-b7e3-66e2c0c6f6c6", // Design
	"0930677c-dd75-424d-9190-b779f3d1c136", // Graphic Design
	"85a78d1f-77e7-468e-a3e5-76ed7c99864b", // Level Design

	"6ea6bca4-4712-4ab9-a906-e3336a9d8039",	// English
];

// Get data: https://api.twitch.tv/helix/streams?first=100&game_id=509670&game_id=509660&after=eyJiIjpudWxsLCJhIjp7Ik9mZnNldCI6MjB9fQ

token_Fetch();

// Build QueryString
$baseQS = "";
qs_Add($baseQS, "first", 100);			// How many to return (max 100)
foreach (GAME_IDS as &$key) {
	qs_Add($baseQS, "game_id", $key);	// Which games to search
}

$request_count = 0;
$data = [];
$response = [];
$pagination = "";

// Step 1: Fetch all the data
do {
	$qs = $baseQS;
	if (strlen($pagination)) {
		qs_Add($qs, "after", $pagination);	// Pagination offset (if available)
	}

	$request = "streams?".$qs;
	$response = fetch_TwitchAPI($request);
	$request_count++;

	if (is_array($response)) {
		//var_dump($response);
		$data = array_merge($data, $response['data']);

		$pagination = "";
		if (array_key_exists('pagination', $response) && array_key_exists('cursor', $response['pagination'])) {
			$pagination = $response['pagination']['cursor'];
		}
	}
	else {
		break;
	}
} while (strlen($pagination));

// Step 2: Parse the data
echo(json_encode($data)."\n");

echo "Found: ".count($data)."\n";
echo "Requests made: $request_count\n";
