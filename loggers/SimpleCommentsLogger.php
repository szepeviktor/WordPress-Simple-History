<?php

/**
 * Logs things related to comments
 */
class SimpleCommentsLogger extends SimpleLogger
{

	public $slug = __CLASS__;

	/**
	 * Get array with information about this logger
	 * 
	 * @return array
	 */
	function getInfo() {

		$arr_info = array(			
			"name" => "Comments Logger",
			"description" => "Logs comments, and modifications to them",
			"capability" => "moderate_comments",
			"messages" => array(

				'anon_comment_added' => _x(
					'{comment_author} ({comment_author_email}) made a comment to post "{comment_post_title}"', 
					'A comment was added to the database by an anonymous internet user',
					'simple-history'
				),

				'user_comment_added' => _x(
					'Added a comment to post "{comment_post_title}"', 
					'A comment was added to the database by a logged in user',
					'simple-history'
				),

				// approve, spam, trash, hold
				'comment_status_approve' => _x(
					'Approved a comment for post "{comment_post_title}"', 
					'A comment was approved',
					'simple-history'
				),

				'comment_status_hold' => _x(
					'Unapproved a comment for post "{comment_post_title}"', 
					'A comment was was unapproved',
					'simple-history'
				),

				'comment_status_spam' => _x(
					'Marked a comment to post "{comment_post_title}" as spam', 
					'A comment was marked as spam',
					'simple-history'
				),

				'comment_status_trash' => _x(
					'Moved a comment to post "{comment_post_title}" to the trash', 
					'A comment was marked moved to the trash',
					'simple-history'
				),

				'comment_untrashed' => _x(
					'Restored a comment to post "{comment_post_title}" from the trash', 
					'A comment was restored from the trash',
					'simple-history'
				),

				'comment_deleted' => _x(
					'Deleted a comment to post "{comment_post_title}"', 
					'A comment was deleted',
					'simple-history'
				),

				'comment_edited' => _x(
					'Edited a comment to post "{comment_post_title}"', 
					'A comment was edited',
					'simple-history'
				),

			)
		);
		
		return $arr_info;

	}

	public function loaded() {

		/**
		 * Fires immediately after a comment is inserted into the database.
		 */
		add_action( 'comment_post', array( $this, 'on_comment_post'), 10, 2 );

		/**
		 * Fires after a comment status has been updated in the database.
		 * The hook also fires immediately before comment status transition hooks are fired.
		 */
		add_action( "wp_set_comment_status", array( $this, 'on_wp_set_comment_status'), 10, 2 );

		/**
		 *Fires immediately after a comment is restored from the Trash.
		 */
		add_action( "untrashed_comment", array( $this, 'on_untrashed_comment'), 10, 1 );

 		/**
 		 * Fires immediately before a comment is deleted from the database.
 		 */
		add_action( "delete_comment", array( $this, 'on_delete_comment'), 10, 1 );

		/**
		 * Fires immediately after a comment is updated in the database.
	 	 * The hook also fires immediately before comment status transition hooks are fired.
	 	 */
		add_action( "edit_comment", array( $this, 'on_edit_comment'), 10, 1 );

		
	}

	public function on_edit_comment($comment_ID) {

		$comment_data = get_comment( $comment_ID );

		if ( is_null( $comment_data ) ) {
			return;
		}

		#sf_d($comment_data);exit;
		
		$comment_parent_post = get_post( $comment_data->comment_post_ID );

		$context = array(
			"comment_ID" => $comment_ID,
			"comment_author" => $comment_data->comment_author,
			"comment_author_email" => $comment_data->comment_author_email,
			"comment_author_url" => $comment_data->comment_author_url,
			"comment_author_IP" => $comment_data->comment_author_IP,
			"comment_content" => $comment_data->comment_content,
			"comment_approved" => $comment_data->comment_approved,
			"comment_agent" => $comment_data->comment_agent,
			"comment_type" => $comment_data->comment_type,
			"comment_parent" => $comment_data->comment_parent,
			"comment_post_ID" => $comment_data->comment_post_ID,
			"comment_post_title" => $comment_parent_post->post_title,
		);

		$this->infoMessage(
			"comment_edited",
			$context
		);		

	}

	public function on_delete_comment($comment_ID) {

		$comment_data = get_comment( $comment_ID );

		if ( is_null( $comment_data ) ) {
			return;
		}
		
		$comment_parent_post = get_post( $comment_data->comment_post_ID );

		$context = array(
			"comment_ID" => $comment_ID,
			"comment_author" => $comment_data->comment_author,
			"comment_author_email" => $comment_data->comment_author_email,
			"comment_author_url" => $comment_data->comment_author_url,
			"comment_author_IP" => $comment_data->comment_author_IP,
			"comment_content" => $comment_data->comment_content,
			"comment_approved" => $comment_data->comment_approved,
			"comment_agent" => $comment_data->comment_agent,
			"comment_type" => $comment_data->comment_type,
			"comment_parent" => $comment_data->comment_parent,
			"comment_post_ID" => $comment_data->comment_post_ID,
			"comment_post_title" => $comment_parent_post->post_title,
		);

		$this->infoMessage(
			"comment_deleted",
			$context
		);		

	}

	public function on_untrashed_comment($comment_ID) {

		$comment_data = get_comment( $comment_ID );

		if ( is_null( $comment_data ) ) {
			return;
		}
		
		$comment_parent_post = get_post( $comment_data->comment_post_ID );

		$context = array(
			"comment_ID" => $comment_ID,
			"comment_author" => $comment_data->comment_author,
			"comment_author_email" => $comment_data->comment_author_email,
			"comment_author_url" => $comment_data->comment_author_url,
			"comment_author_IP" => $comment_data->comment_author_IP,
			"comment_content" => $comment_data->comment_content,
			"comment_approved" => $comment_data->comment_approved,
			"comment_agent" => $comment_data->comment_agent,
			"comment_type" => $comment_data->comment_type,
			"comment_parent" => $comment_data->comment_parent,
			"comment_post_ID" => $comment_data->comment_post_ID,
			"comment_post_title" => $comment_parent_post->post_title,
		);

		$this->infoMessage(
			"comment_untrashed",
			$context
		);		

	}

	/**
	 * Fires after a comment status has been updated in the database.
	 * The hook also fires immediately before comment status transition hooks are fired.
	 * @param int         $comment_id     The comment ID.
	 * @param string|bool $comment_status The comment status. Possible values include 'hold',
	 *                                    'approve', 'spam', 'trash', or false.
	 * do_action( 'wp_set_comment_status', $comment_id, $comment_status );
	 */	
	public function on_wp_set_comment_status($comment_ID, $comment_status) {

		$comment_data = get_comment( $comment_ID );

		if ( is_null( $comment_data ) ) {
			return;
		}

		// WP_Post object
		$comment_parent_post = get_post( $comment_data->comment_post_ID );

		/*
		$comment_status:
			approve
				comment was approved
			spam
				comment was marked as spam
			trash
				comment was trashed
			hold
				comment was un-approved
		*/
		// sf_d($comment_status);exit;
		$message = "comment_status_{$comment_status}";

		$context = array(
			"comment_ID" => $comment_ID,
			"comment_author" => $comment_data->comment_author,
			"comment_author_email" => $comment_data->comment_author_email,
			"comment_author_url" => $comment_data->comment_author_url,
			"comment_author_IP" => $comment_data->comment_author_IP,
			"comment_content" => $comment_data->comment_content,
			"comment_approved" => $comment_data->comment_approved,
			"comment_agent" => $comment_data->comment_agent,
			"comment_type" => $comment_data->comment_type,
			"comment_parent" => $comment_data->comment_parent,
			"comment_post_ID" => $comment_data->comment_post_ID,
			// the post that this is a comment to
			"comment_post_title" => $comment_parent_post->post_title,
		);

		$this->infoMessage(
			$message,
			$context
		);

	}

	public function on_comment_post($comment_ID, $comment_approved) {

		$comment_data = get_comment( $comment_ID );

		// WP_Post object
		$comment_parent_post = get_post( $comment_data->comment_post_ID );

		$context = array(
			"comment_ID" => $comment_ID,
			"comment_author" => $comment_data->comment_author,
			"comment_author_email" => $comment_data->comment_author_email,
			"comment_author_url" => $comment_data->comment_author_url,
			"comment_author_IP" => $comment_data->comment_author_IP,
			"comment_content" => $comment_data->comment_content,
			"comment_approved" => $comment_data->comment_approved,
			"comment_agent" => $comment_data->comment_agent,
			"comment_type" => $comment_data->comment_type,
			"comment_parent" => $comment_data->comment_parent,
			"comment_post_ID" => $comment_data->comment_post_ID,
			// the post that this is a comment to
			"comment_post_title" => $comment_parent_post->post_title,
		);

		$message = "";
		if ($comment_data->user_id) {
			// comment was from a logged in user
			$message = "user_comment_added";

		} else {
			// comment was from a non-logged in user
			$message = "anon_comment_added";
			$context["_initiator"] = SimpleLoggerLogInitiators::WEB_USER;
		}

		$this->infoMessage(
			$message,
			$context
		);

	}

}