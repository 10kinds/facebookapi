<?php 

$pageID = "Disanddemhattiesburg";
$access_token = "449025051937690|Q9WJHCYHhDyUr8Cuv6nodvRQO5Y";
$fields = "id,name,about,phone,website,emails,contact_address,location,founded,start_info,category,hours,likes,talking_about_count";

$json_link = "https://graph.facebook.com/$pageID?fields=$fields&access_token={$access_token}";
$json = file_get_contents($json_link);
$obj = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);


?>

<pre><? print_r($obj); ?></pre>