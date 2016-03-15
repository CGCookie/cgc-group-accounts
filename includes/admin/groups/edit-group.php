<?php
$group_id = absint( $_GET['group'] );
$group    = cgc_group_accounts()->groups->get( $group_id );
$owner    = get_userdata( $group->owner_id );
?>
<div class="wrap">

	<h2>Edit Group</h2>
	
	<form method="post">

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="name">Name</label>
				</th>

				<td>
					<input type="text" name="name" id="name" class="regular-text" autocomplete="off" value="<?php echo esc_attr( stripslashes( $group->name ) ); ?>"/>
					<p class="description">The name of this group</p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="description">description</label>
				</th>

				<td>
					<?php wp_editor( wp_kses_post( wptexturize( $group->description )  ), 'description', array( 'textarea_name' => 'description' ) ); ?>
					<p class="description">The description of this group</p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="user_name">Group Owner</label>
				</th>

				<td>
					<input type="text" disabled="disabled" class="cgc-user-search" autocomplete="off" value="<?php echo esc_attr( $owner->user_login ); ?>" />
					<p class="description">Group owners cannot be changed.</p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="seats">Seats</label>
				</th>

				<td>
					<input type="number" min="1" step="1" name="seats" id="seats" class="regular-text" autocomplete="off" value="<?php echo absint( $group->seats ); ?>" />
					<p class="description">The number of seats for this group</p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="expiration">Expiration</label>
				</th>

				<td>
					<input type="date" name="expiration" id="expiration" class="regular-text" autocomplete="off" value="<?php echo $group->expiration; ?>" />
					<p class="description">The expiration for this group.</p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="access_level">Access Level</label>
				</th>

				<td>
					<input type="text" name="access_level" id="access_level" class="regular-text" autocomplete="off" value="<?php echo $group->access_level; ?>" />
					<p class="description">Set an access level for this group</p>
				</td>

			</tr>
		</table>

		<input type="hidden" name="group" id="group" value="<?php echo esc_attr( $group->group_id ); ?>" />
		<input type="hidden" name="cgcg-action" value="edit-group" />

		<?php submit_button( 'Update Group' ); ?>

	</form>

</div>
