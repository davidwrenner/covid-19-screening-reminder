<?php
$fp = "/home/davidwrenner/covid-reminder/numbers";

$number = (string)$_POST["user"];
$time = (string)$_POST["time"];
$days = (string)$_POST["days"];

// input validation
if (!preg_match("/^[\d]{3}-[\d]{3}-[\d]{4}$/", $number)){
    echo json_encode(array(
		"success" => false,
		"message" => "Bad input for phone number: ".$number
	));
	exit;
}
if (!preg_match("/^[\d]{2}:[\d]{2}$/", $time)){
    echo json_encode(array(
		"success" => false,
		"message" => "Bad input for phone number: ".$time
	));
	exit;
}
if (!preg_match("/^[0-1]{7}$/", $days)){
    echo json_encode(array(
		"success" => false,
		"message" => "Bad input for days of the week: ".$days
	));
	exit;
}

// translate time to be compatible with Twilio
// i.e. 000-000-0000 --> +10000000000
$time = "+1".substr($time, 0, 2).substr($time, 4, 6).substr($time, 8, 11);

// add new entry as a single line of comma-delineated information
$new_entry = $number.",".$time.",".$days

$f = fopen($fp, 'a');
if (fwrite($f, $new_entry)) {
    fclose($f);
    echo json_encode(array(
		"success" => true,
		"message" => "Added number ".$number." to server."
	));
	exit;
}
else {
    echo json_encode(array(
		"success" => false,
		"message" => "Error writing new number to file."
	));
	exit;
}


?>