<?php
header("Content-Type: application/json");
$json_str = file_get_contents("php://input");
$post_obj = json_decode($json_str, true);

$number = (string) $post_obj["number"];
$time = (string) $post_obj["time"];
$days = (string) $post_obj["days"];

$fp = "/home/davidwrenner/covid-reminder/numbers";

// input validation
if (!preg_match("/^[\d]{3}-[\d]{3}-[\d]{4}$/", $number)) {
    echo json_encode(array(
		"success" => false,
		"message" => "Bad input for phone number: ".$number
	));
	exit;
}
if (!preg_match("/^[\d]{2}:[\d]{2}$/", $time)) {
    echo json_encode(array(
		"success" => false,
		"message" => "Bad input for time: ".$time
	));
	exit;
}
$hour = intval(substr($time, 0, 1));
$minute = intval(substr($time, 3, 2));
if ($hour > 23 || $hour < 0) {
    echo json_encode(array(
		"success" => false,
		"message" => "Bad hour input for time: ".$time
	));
	exit;
}
if ($minute > 59 || $minute < 0) {
    echo json_encode(array(
		"success" => false,
		"message" => "Bad minute input for time: ".$time
	));
	exit;
}
if (!preg_match("/^[0-1]{7}$/", $days)) {
    echo json_encode(array(
		"success" => false,
		"message" => "Bad input for days of the week: ".$days
	));
	exit;
}

// translate time to be compatible with Twilio
// i.e. 000-000-0000 --> +10000000000
$twilio_number = "+1".substr($number, 0, 3).substr($number, 4, 3).substr($number, 8, 4);

// add new entry as a single line of comma-delineated information
$new_entry = $twilio_number.",".$time.",".$days."\n";

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