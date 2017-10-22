<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is simply JS -> PHP connector comand switch
 *
*/
// $home must be true
if(!$home){exit;}

// Initialize connector class
$once = new core($_CONFIG);
$once->set_data(array("ajax" => true, "csrf_token" => $_GET['csrf_token']));

switch($_GET['o']){
	case 'category_new':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->category_new($once->filter_string($_GET['module']));
	}break;
	case 'category_delete':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->category_delete($once->filter_string($_GET['module']));
	}break;
	case 'category_edit':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"name" => $once->filter_string($_POST['name']),
			"ico" => $once->filter_string($_POST['ico'])
		));
		$once->category_edit($once->filter_string($_GET['module']));
	}break;
	case 'category_sort':{
		$once->set_data(array(
			"category" => $_POST['category']
		));
		$once->category_sort($once->filter_string($_GET['module']));
	}break;
	case 'category_change':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"category_id" => intval($_GET['category_id'])
		));
		$once->category_change($once->filter_string($_GET['module']));
	}break;

	case 'type_new':{
		$once->type_new($once->filter_string($_GET['module']));
	}break;
	case 'type_delete':{
		$once->set_data(array(
			"id" => intval($_GET['id'])
		));
		$once->type_delete($once->filter_string($_GET['module']));
	}break;
	case 'type_edit':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"name" => $once->filter_string($_POST['name']),
			"action" => $once->filter_string($_POST['action']),
			"ico" => $once->filter_string($_POST['ico'])
		));
		$once->type_edit($once->filter_string($_GET['module']));
	}break;
	case 'type_sort':{
		$once->set_data(array(
			"type" => $_POST['type']
		));
		$once->type_sort($once->filter_string($_GET['module']));
	}break;
	case 'type_change':{
		$once->set_data(array(
			"id" => intval($_GET['id']),
			"type_id" => intval($_GET['type_id'])
		));
		$once->type_change($once->filter_string($_GET['module']));
	}break;

	default:
		echo 'wrong connector command';
	break;
}
?>
