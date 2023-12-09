<?php
// handle requests to /product/:id 
if (preg_match("/^\/product\/[a-zA-Z0-9]+$/", $_SERVER["REQUEST_URI"], $matches)) {
	$parts = explode("/", $matches[0]);
	$id = htmlspecialchars(end($parts));
	include "./_product.php";
} elseif ($_SERVER["REQUEST_URI"] === "/") {
	include "./home.php";
} else {
	// TODO: make proper 404 page
	http_response_code(404);
	echo "404 Not Found";
}

