<?php
$group_id = cgc_group_accounts()->members->get_group_id();
$role     = cgc_group_accounts()->members->get_role();
?>

<h3>Manage Group Account</h3>
<p>Your group account control panel. Add, remove, and promote group members!</p>
<script type="text/javascript">
jQuery( document ).ready( function($) {
	$('#group-add-member-confirmation a.close-modal').click(function(e) {
		e.preventDefault();
		$('a.close-reveal-modal').trigger('click');
	});
});
</script>
<form method="post" id="add-group-member-form">

	<p><strong>Add a member to your group</strong></p>

	<p>Enter the email address of a user account to add to the group. This email must be already registered with a user account.</p>

	<p>
		<input type="text" name="user_email" id="user_email" autocomplete="off" />
		<input type="hidden" name="group" id="group" value="<?php echo absint( $group_id ); ?>" />
		<input type="hidden" name="cgcg-action" value="add-member" />
		<a href="#" data-reveal-id="group-add-member-confirmation" data-dismissmodalclass="close-modal">Add Member</a>

	</p>

	<div id="group-add-member-confirmation" class="reveal-modal">
		<h4>Add a new member to your group</h4>
		<div class="group-member-gravatar">

		</div>
		<p><strong>Confirm adding this member</strong></p>
		<p>By adding this user to your group membership, one seat will be reduced from your available</p>

		<a href="#" class="close-modal">Nah, nevermind</a>
		<input type="submit" value="Add Member" />

		<p><em>By adding this member to your account, you agree to the group <a href="#">terms of use</a>.</em></p>
		<a class="close-reveal-modal">&#215;</a>
	</div>

</form>

<form method="post" id="import-group-members-form" enctype="multipart/form-data">

	<p><strong>Import a CSV of members into your group</strong></p>

	<p>Bulk import accounts from a CSV file. <a href="https://s3.amazonaws.com/cgc-cdn-bucket-01/groups/cgc-group-example.csv">Click here to see a sample CSV</a>.</p>

	<p>
		<input type="file" name="group_csv" id="group_csv"/>
		<input type="hidden" name="group" id="group" value="<?php echo absint( $group_id ); ?>" />
		<input type="hidden" name="cgcg-action" value="import-members" />
		<input type="submit" value="Import CSV" />
	</p>

</form>

<?php if( 'owner' === $role || 'admin' === $role ) : ?>
<table class="rcp-table" id="rcp-group-dashboard-members">
	<thead>
		<tr>
			<th>Name</th>
			<th>Role</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach( cgc_group_accounts()->groups->get_members( $group_id ) as $member ) : ?>
		
		<?php
		$user_data = get_userdata( $member->user_id );
		if( ! $user_data ) {
			continue;
		}

		$i = 1;
		?>

		<tr<?php echo $i & 1 ? ' class="alternate"' : ''; ?>>
			<td><?php echo $user_data->display_name; ?></td>
			<td><?php echo $member->role; ?></td>
			<td>
				<?php if( 'owner' != $member->role ) : ?>
					<a href="<?php echo esc_url( home_url( 'index.php?cgcg-action=remove-member&group=' . $member->group_id . '&member=' . $member->user_id ) ); ?>">Remove from Group</a>&nbsp;|&nbsp;
					<?php if( 'admin' == $member->role ) : ?>
						<a href="<?php echo esc_url( admin_url( 'index.php?cgcg-action=make-member&group=' . $member->group_id . '&member=' . $member->user_id ) ); ?>">Set as Member</a>
					<?php else : ?>
						<a href="<?php echo esc_url( admin_url( 'index.php?cgcg-action=make-admin&group=' . $member->group_id . '&member=' . $member->user_id ) ); ?>">Set as Admin</a>
					<?php endif; ?>
				<?php endif; ?>
			</td>
		</tr>
		<?php $i++; endforeach; ?>
	</tbody>
</table>
<?php endif; ?>