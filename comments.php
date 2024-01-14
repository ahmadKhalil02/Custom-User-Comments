/**
* Create the field inside the edit user page (admin)
*/
function comment_user_field($user) {
	?>
	 <div style="padding-top: 40px; padding-bottom: 40px;">
	<h3>Comments for customers</h3>
	<p>Only admins can see the comments</p>
	<table class="form-table">
		<tr>
			<th><label for="comment-field">Comments</label></th>
			<td>
				<textarea name="comment-field" id="comment-field" rows="3" cols="30"
						  class="regular-text"><?php echo esc_textarea(get_the_author_meta('comment-field', $user->ID)); ?></textarea><br />
				<span class="description">Wrtie a comment for the customer. <br> example special shipping.</span>
			</td>
		</tr>
	</table>
	</div>
	<?php
}

add_action('show_user_profile', 'comment_user_field');
add_action('edit_user_profile', 'comment_user_field');

/**
* Save the field
*/
function save_comment_user_field($user_id) {

if ( !current_user_can( 'edit_user', $user_id ) ) {
	return false;
}

$new_comment = isset($_POST['comment-field']) ? sanitize_text_field($_POST['comment-field']) : '';
$old_comment = get_user_meta($user_id, 'comment-field', true);

if ($new_comment === '') {
	delete_user_meta($user_id, 'comment-field');
} elseif ($new_comment !== $old_comment) {
	update_user_meta($user_id, 'comment-field', $new_comment);
}
}

add_action('personal_options_update', 'save_comment_user_field');
add_action('edit_user_profile_update', 'save_comment_user_field');



/**
 * Display message in the order details page (admin)
 */

add_action('woocommerce_admin_order_data_after_payment_info', 'display_comment_in_order_admin');
function display_comment_in_order_admin($order) {
    // Get the user ID from the order
    $user_id = $order->get_user_id();

    // If there's no user ID (guest order), display a default message
    if (!$user_id) {
        echo '<p>Gästkonto, ingen kommentar hittad.</p>';
        return;
    }

    // Retrieve the user meta
    $comment = get_user_meta($user_id, 'comment-field', true);

    // Check if there's a comment and display it
    if (!empty($comment)) {
        echo '<p style="color:black; font-size: 14px; font-weight: 600;"><strong style="color: red; font-size: 14px; font-weight: 700;">There is a special comment for the user:</strong> <br> ' . esc_html($comment) . '</p>';
    } else {
       echo '<p style="color:green; font-size: 14px; font-weight: 600;">No comments found for the user.</p>';
    }
}
