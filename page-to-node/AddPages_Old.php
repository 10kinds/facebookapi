<?php

// Removed the Http request info and the SQL Insert as those is no longer needed

$search_on = str_replace(" ", "+", $search_on);

$city_filter = "Hattiesburg";
$now = date("Y-m-d H:i:s");


//Create the Events Array
$formatted_array[] = $columns;

// Ready to hit the Graph
$json_link = "https://graph.facebook.com/search?pretty=0&q={$search_on}&type=page&access_token={$access_token}&limit=$limit";
$json = file_get_contents($json_link);


// Decode JSON
$obj = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
// count the number of events
$page_count = count($obj['data']);

echo "<h3>We Found " . $page_count . " Page(s).</h3>";

// Load the page ids into an array so we can search them
for($x=0; $x<$page_count; $x++){
	 
	$name = $obj['data'][$x]['name'];
	$id = $obj['data'][$x]['id'];
	$pages[] = array($name,$id);
}

// How man pages do we have?
$arrlength=count($pages);

for($x = 0; $x < $arrlength; $x++) {
	if (isset($pages[$x][1]) === true && empty($pages[$x][1]) === false) {
		$json_link2 = "https://graph.facebook.com/{$pages[$x][1]}?fields=$fields&access_token={$access_token}";
		$json2 = file_get_contents($json_link2);
		$obj2 = json_decode($json2, true, 512, JSON_BIGINT_AS_STRING);
		$pages2[] = array($obj2);
	}
}

$arrlength=count($pages2);


for($x = 0; $x < $arrlength; $x++) {

	$id = $pages2[$x][0]['id'];
	$name = $pages2[$x][0]['name'];
	$about = $pages2[$x][0]['about'];
	$phone = $pages2[$x][0]['phone'];
	$website = $pages2[$x][0]['website'];
	$email = $pages2[$x][0]['emails'][0];
	$city = $pages2[$x][0]['location']['city'];
	$country = $pages2[$x][0]['location']['country'];
	$latitude = $pages2[$x][0]['location']['latitude'];
	$longitude = $pages2[$x][0]['location']['longitude'];
	$state = $pages2[$x][0]['location']['state'];
	$state = convert_state($state);
	$street = $pages2[$x][0]['location']['street'];
	$zip = $pages2[$x][0]['location']['zip'];
	$category = $pages2[$x][0]['category'];


	if ($city != $city_filter || in_array($id, $pageids)) {}
	else{
		$formatted_array[] = array($id, $name, $about, $phone, $website, $email, $city, $country, $latitude, $longitude, $state, $street, $zip, $category,$search_on,$now,0);

	}
}


 

// Build the Events string
$fields = implode(', ', array_shift($formatted_array));

$values = array();
foreach ($formatted_array as $rowValues) {
	foreach ($rowValues as $key => $rowValue) {
		$rowValues[$key] = addslashes($rowValues[$key]);
	}

	$values[] = "('" . implode("', '", $rowValues) . "')";
}