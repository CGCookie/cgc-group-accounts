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
					<input type="text" name="description" id="description" class="regular-text" autocomplete="off" value="<?php echo esc_attr( stripslashes( $group->description ) ); ?>"/>
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

		</table>

		<input type="hidden" name="group" id="group" value="<?php echo esc_attr( $group->group_id ); ?>" />
		<input type="hidden" name="cgcg-action" value="edit-group" />

		<?php submit_button( 'Update Group' ); ?>

	</form>

</div>
