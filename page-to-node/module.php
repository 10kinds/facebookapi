<?php

/**
 * Implements hook_permission().
 */

function facebook_pagenode_permission() {
  return array(
	'administer facebook pagenode' => array(
	  'title' => t('Administer the Facebook Page to Node Module'),
	  'description' => t('Perform administration tasks for the Facebook Page to Node Module.'),
	),
  );
}

/**
 * Implements hook_help().
 */

function facebook_pagenode_help($path, $arg) {
  switch ($path) {
	case "admin/help#facebook_pagenode":
	  return '' . t("Displays links to nodes created on this date") . '';
      break;
  }
}

/**
 * Implements hook_menu().
 */

function facebook_pagenode_menu() {
	$items = array();
	
	$items['pagenode'] = array (
	  'title' => 'Facebook Page to Node',
	  'page callback' => 'facebook_pagenode_base',
	  'access arguments' => array ('acce content'),
	);
	
	$items['pagenode/%'] = array (
	  'title' => 'Process Page to Node',
	  'page callback' => 'facebook_pagenode_addid',
	  'page arguments' => array(1),
	  'file' => 'facebook_pagenode.extra.inc',			
	);
	
	return $items;
}

function facebook_pagenode_base() {
	$content = array();
	
	$content['base_page'] = array(
	  '#type' => 'markup',
	  '#markup' => '<p>Facebook Page to Node.</p>',
	);
	
	return $content;
}





