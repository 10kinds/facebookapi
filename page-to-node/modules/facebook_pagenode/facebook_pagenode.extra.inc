<?php

function facebook_pagenode_addid($pageID){

  $access_token = "449025051937690|Q9WJHCYHhDyUr8Cuv6nodvRQO5Y";
  $fields = "id,name,about,phone,website,emails,contact_address,location,category,hours,link,cover";
  $json_link = "https://graph.facebook.com/$pageID?fields=$fields&access_token={$access_token}";
  $json = file_get_contents($json_link);
  $obj = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);

  // Make variable from our specific data points in $obj
  $id = isset($obj['id']) ? $obj['id'] : 0;
  $name = isset($obj['name']) ? $obj['name'] : '';
  $about = isset($obj['about']) ? $obj['about'] : '';
  $phone = isset($obj['phone']) ? $obj['phone'] : '';
  $website = isset($obj['website']) ? $obj['website'] : '';
  $email = isset($obj['emails'][0]) ? $obj['emails'][0] : '';
  $city = isset($obj['location']['city']) ? $obj['location']['city'] : '';
  $country = isset($obj['location']['country']) ? $obj['location']['country'] : '';
  $latitude = isset($obj['location']['latitude']) ? $obj['location']['latitude'] : 0.00;
  $longitude = isset($obj['location']['longitude']) ? $obj['location']['longitude'] : 0.00;
  $USstate = isset($obj['location']['state']) ? $obj['location']['state'] : '';
  $street = isset($obj['location']['street']) ? $obj['location']['street'] : '';
  $zip = isset($obj['location']['zip']) ? $obj['location']['zip'] : '';
  $category = isset($obj['category']) ? $obj['category'] : '';
  $photo = isset($obj['cover']['source']) ? $obj['cover']['source'] : '';
  $link = isset($obj['link']) ? $obj['link'] : '';

  // Begin building the node object
  $node = new stdClass(); // We create a new node object
  $node->type = "venues"; // Or any other content type you want
  $node->title = $name;
  $node->language = LANGUAGE_NONE; // Or any language code if Locale module is enabled. More on this below *

  // Set some default values.
  node_object_prepare($node);
  $node->uid = 1;

  // Let's add standard body field
  $node->body[$node->language][0]['value'] = $about;
  $node->body[$node->language][0]['summary'] = $about;
  $node->body[$node->language][0]['format'] = 'filtered_html';

  // Load our custom fields
  $node->{'field_fb_id'}[$node->language][0]['value'] = $id;
  $node->{'field_phone'}[$node->language][0]['value'] = $phone;
  $node->{'field_website'}[$node->language][0]['value'] = $website;
  $node->{'field_email'}[$node->language][0]['value'] = $email;
  $node->{'field_phone'}[$node->language][0]['value'] = $phone;


  // The Address fields
  $node->{'field_address'}[$node->language][0]['country']['value'] = 'US';
  $node->{'field_address'}[$node->language][0]['thoroughfare']['value'] = $street;
  $node->{'field_address'}[$node->language][0]['administrative_area']['value'] = $USstate;
  $node->{'field_address'}[$node->language][0]['locality']['value'] = $city;
  $node->{'field_address'}[$node->language][0]['postal_code']['value'] = $zip;
  // Geolocation
  $node->{'field_geolocation'}[$node->language][0]['lat'] = $latitude;
  $node->{'field_geolocation'}[$node->language][0]['lng'] = $longitude;


  // set our temporary path
  if ($wrapper = file_stream_wrapper_get_instance_by_uri('public://')) {
    $file_path = $wrapper->realpath();
    $file_path = $file_path . '/fb_covers/temp';
  }
  // create the fb_covers folder if it doesn't exist
  if (!file_exists($file_path)) {
    mkdir($file_path, 0755, true);
  }
  // Go get the FB cover photo
  $getphoto = file_get_contents($photo);
  $file_path = $file_path . '/' . $id . '.jpg';
  file_put_contents($file_path, $getphoto);

  $file = (object) array(
    'uid' => 1,
    'uri' => $file_path,
    'filemime' => file_get_mimetype($file_path),
    'status' => 1,
  );

  // copy from the temp path to the permanent folder
  $file = file_copy($file, 'public://fb_covers');
  $node->{'field_image'}[$node->language][0] = (array)$file;
  // We're done with the image now. Let's wrap this up.

  // Get the tid and assign it. If it doesn't exist, then create it.
  $term_array = taxonomy_get_term_by_name($category);
  if(empty($term_array)) {
  	$vocab = taxonomy_vocabulary_machine_name_load('categories');
    $new_term = (object) array(
      'name' => $category,
      'description' => $category . ' was autocreated when ' . $name . ' was imported.',
      'vid' => $vocab->vid,
    );

    taxonomy_term_save($new_term);
    $tid = $new_term->tid;
  } else {
    $tid_obj = reset($term_array);
    $tid = $tid_obj->tid;
  }

  $node->{'field_fb_pg_category'}[$node->language][]['tid'] = $tid;

  // Include node.pages to be able to get the node add form.
  module_load_include('inc', 'node', 'node.pages');
  $node_form = node_add($node->type);

  // Create the form state for validation.
  $form_state = array(
    'values' => (array) $node,
  );

  // Validate the form.
  node_form_validate($node_form, $form_state);

  // Get the form errors.
  $form_errors = form_get_errors();

  // If there are no errors proceed to save the node.

  if (empty($form_errors)) {

    // Prepare node for a submit
    $node = node_submit($node);
    node_save($node);
  }

	$content = array (
	  '#type' => 'markup',
      '#markup' => drupal_set_message('<pre>' . print_r($node, 1) . '</pre>'),
	);

	return $content;

}
