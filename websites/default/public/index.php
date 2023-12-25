<?php

function parse_uri(): array
{
	$uri = explode("?", $_SERVER["REQUEST_URI"])[0] ?? "";
	return explode("/", trim($uri, "/"));
}

function not_found(): void
{
	http_response_code(404);
	echo "404 Not Found";
	exit;
}

$parts = parse_uri();

// The proxy is setup to redirect pages not found to index.php so we will be taking advantage of that for cleaner URLs and to make it easier to handle routing
if (count($parts) == 2) {
	$resource = $parts[0];
	if ($resource === "product") {
		$public_id = htmlspecialchars($parts[1]);
		include __DIR__ . "/_product.php";
		exit;
	} elseif ($resource === "category") {
		$slug = htmlspecialchars($parts[1]);
		include __DIR__ . "/_category.php";
		exit;
	} else {
		not_found();
	}
} elseif ($_SERVER["REQUEST_URI"] === "/" || empty($parts)) {
	include __DIR__ . "/home.php";
	exit;
} else {
	not_found();
}
