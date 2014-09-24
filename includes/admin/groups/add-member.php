<?php $group_id = absint( $_GET['group'] ); ?>
<div class="wrap">

	<h2>New Group Member</h2>
	
	<form method="post">

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="user_email"><?php _e( 'User Email', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="user_email" id="user_email" class="cgc-user-search" autocomplete="off" />
					<p class="description">Enter the email address of a user account to add to the group.</p>
				</td>

			</tr>

		</table>

		<input type="hidden" name="group" id="group" value="<?php echo absint( $group_id ); ?>" />
		<input type="hidden" name="cgcg-action" value="add-member" />

		<?php submit_button( 'Add Member' ); ?>

	</form>

</div>
