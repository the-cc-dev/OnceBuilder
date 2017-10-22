<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is core connector class
 *
*/

class once extends core{

	#### GET POSTS DATA
	function get_short_posts_data(){
		// Prepare statements to get posts.
		$stmt = $this->pdo->prepare("SELECT id, title, name FROM edit_posts ORDER by id DESC LIMIT 10");
		$stmt->execute();
		
		$data['items'] = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$data['items'][] = $row;
		}
		
		return $data;
	}
	function get_posts_data(){
		// Prepare statements to get posts.
		$stmt = $this->pdo->prepare("SELECT title FROM edit_posts LIMIT 0, 10");
		$stmt->execute();
		
		$data['items'] = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$data['items'][] = $row;
		}
		
		return $data;
	}
	function get_post_content(){
		// Prepare statements to get selected id.
		$stmt = $this->pdo->prepare("SELECT title, source FROM edit_posts WHERE id=:id LIMIT 1");
		$stmt->bindParam('id', $this->data['id'], PDO::PARAM_INT);
		$stmt->execute();

		while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$this->items[] = $row;
		}

		// Check if post exist.
		if($this->items[0]){
				
				
		}
		
		return $data;
	}

	function bulk_action(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Prepare statements to star selected id.
			$stmt1 = $this->pdo->prepare("UPDATE edit_pages SET stared = 1 WHERE id=:id");

			// Prepare statements to unstar selected id.
			$stmt2 = $this->pdo->prepare("UPDATE edit_pages SET stared = 0 WHERE id=:id");

			// Prepare statements to delete selected id.
			$stmt3 = $this->pdo->prepare("DELETE FROM edit_pages WHERE id=:id");

			// Loop bulk items and make action
			foreach ($this->data['ids'] as $position => $item){
				$obj['ids'][]=$position;
				// Check action type then do it
				if($this->data['action']=='star'){
					$stmt1->bindParam(':id', $position, PDO::PARAM_INT);
					$stmt1->execute();
				}else if($this->data['action']=='unstar'){
					$stmt2->bindParam(':id', $position, PDO::PARAM_INT);
					$stmt2->execute();
				}else if($this->data['action']=='delete'){
					$stmt3->bindParam(':id', $position, PDO::PARAM_INT);
					$stmt3->execute();

					if($stmt3->rowCount()){
						$this->recurse_delete($this->data['root_path'].'/once/pages/'.$position.'');
						}
				}
			}
		}
		return $this->once_response();
	}

	function item_delete(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Use once to delete item
			if($this->once_delete_item('posts')){
				// ok
			}
		}
		return $this->once_response();
	}
	function item_edit(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Prepare statements to get post.
			$stmt = $this->pdo->prepare("SELECT * FROM edit_posts WHERE id=:id AND user_id=:user_id LIMIT 1");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

			// Check if post exist
			if($this->item){
				// Prepare statements to update post.
				$stmt = $this->pdo->prepare("UPDATE edit_posts SET type_id=:type_id, title=:title, keywords=:keywords, description=:description WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->item['id'], PDO::PARAM_INT);
				$stmt->bindParam(':type_id', $this->data['type_id'], PDO::PARAM_INT);
				$stmt->bindParam(':title', $this->data['title'], PDO::PARAM_STR, 255);
				$stmt->bindParam(':keywords', $this->data['keywords'], PDO::PARAM_STR, 255);
				$stmt->bindParam(':description', $this->data['description'], PDO::PARAM_STR, 255);
				$stmt->execute();
			}else{
				$this->set_error('Post not exist');
			}
		}
		return $this->once_response();
	}
	function item_edit_content(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Prepare statements to get post.
			$stmt = $this->pdo->prepare("SELECT * FROM edit_posts WHERE id=:id AND user_id=:user_id LIMIT 1");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
			$stmt->bindParam(':user_id', $this->data['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$this->item = $stmt->fetch(PDO::FETCH_ASSOC);

			// Check if post exist
			if($this->item){
				// Prepare statements to update post.
				$stmt = $this->pdo->prepare("UPDATE edit_posts SET source=:source WHERE id=:id LIMIT 1");
				$stmt->bindParam(':id', $this->item['id'], PDO::PARAM_INT);
				$stmt->bindParam(':source', $this->data['source'], PDO::PARAM_STR, 2550);
				$stmt->execute();
			}else{
				$this->set_error('Post not exist');
			}
		}
		return $this->once_response();
	}
	function item_new(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Use once to insert empty item
			if($this->once_insert_item('posts')){
				// ok
			}
		}
		return $this->once_response();
	}
	function item_star(){
		if($this->once_csrf_token_check($this->data['csrf_token']) && $this->once_creator_check()){
			// Prepare statements to get all layers
			$stmt = $this->pdo->prepare("SELECT * FROM edit_posts WHERE id=:id LIMIT 1");
			$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
			$stmt->execute();
				
			// Get count of returned records
			if($stmt->rowCount()){
				$this->item=$stmt->fetch(PDO::FETCH_ASSOC);
				// Check if its stared/unstared then unstar/star
				if($this->item['stared']==1){
					// Prepare statements to unstar selected data
					$stmt = $this->pdo->prepare("UPDATE edit_posts SET stared=0 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}else{
					// Prepare statements to star selected data
					$stmt = $this->pdo->prepare("UPDATE edit_posts SET stared=1 WHERE id=:id LIMIT 1");
					$stmt->bindParam(':id', $this->data['id'], PDO::PARAM_INT);
					$stmt->execute();
				}
			}else{
				$this->set_error('Post not exist');
			}
		}
		return $this->once_response();
	}
}
?>