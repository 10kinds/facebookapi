<?php 
function facebook_pagenode_form($form, &$form_state) {
  
  	$form['fbid'] = array(
    '#type' => 'textfield',
    '#title' => 'FB Page ID',
    '#size' => 50,
    '#maxlength' => 50,
    '#required' => TRUE,
  );
  
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Add Page'),
  );
  
  return $form;
}

function facebook_pagenode_form_validate($form, &$form_state) {
	if (!empty($form_state['values']['fbid'])){
	    form_set_error('price', t('No Facebook Page ID Entered.'));
	  }
// Need to add validation to see if FB ID already exists in Venues content type
}

function facebook_pagenode_form_submit($form, &$form_state) {
	drupal_set_message(t('The form has been submitted.'));
}


function facebook_pagenode_fetchpage() {


	$node = new stdClass(); // We create a new node object
	$node->type = "venues"; // Or any other content type you want
	$node->title = "Your title goes jere";
	$node->language = LANGUAGE_NONE; // Or any language code if Locale module is enabled. More on this below *
	$node->path = array('alias' => 'your node path'); // Setting a node path
	node_object_prepare($node); // Set some default values.
	$node->uid = 1; // Or any id you wish



// Let's add standard body field
	$node->body[$node->language][0]['value'] = 'This is a body text';
	$node->body[$node->language][0]['summary'] = 'Here goes a summary';
	$node->body[$node->language][0]['format'] = 'filtered_html'; // If field has a format, you need to define it. Here we define a default filtered_html format for a body field

// Let's add some CCK/Fields API field. This is pretty similar to the body example 
	$node->field_custom_name[$node->language][0]['value'] = 'This is a custom field value';

// If your custom field has a format, don't forget to define it here
	$node->field_custom_name[$node->language][0]['format'] = 'This is a custom field value';

// And etc. you can add as much fields here as your content type has. The sky is the limit... and the server specs, of course ;)

//// Images
// Some file on our system
	$file_path = drupal_realpath('somefile.png'); // Create a File object
	$file = (object) array(
	  'uid' => 1,
	  'uri' => $file_path,
	  'filemime' => file_get_mimetype($file_path),
	  'status' => 1,
	); 
	$file = file_copy($file, 'public://'); // Save the file to the root of the files directory. You can specify a subdirectory, for example, 'public://images' 
	$node->field_image[LANGUAGE_NONE][0] = (array)$file; //associate the file object with the image field:
	
// Add a term to the node	
	$node->field_tags[$node->language][]['tid'] = 1;
	
// Save the node
	$node = node_submit($node); // Prepare node for a submit
	node_save($node); // After this call we'll get a nid
	
//// Create a taxonomy term
//$term = new stdClass();
//$term->name = &lsquo;Term Name&rsquo;;
//$term->vid = 1; // &lsquo;1&rsquo; is a vocabulary id you wish this term to assign to
//$term->field_custom_field_name[LANGUAGE_NONE][0]['value'] = &lsquo;Some value&rsquo;; // OPTIONAL. If your term has a custom field attached it can added as simple as this
//taxonomy_term_save($term); // Finally, save our term

}