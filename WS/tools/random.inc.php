<?php

function get_token() {
	return get_random_string();
}

function create_uploaded_filename() {
	return get_random_string(64);
}


function get_random_string($len=32) {
	return bin2hex(openssl_random_pseudo_bytes($len, $cstrong));
}

function get_pincode() {
	return 1000 + (rand() % 9000);
}