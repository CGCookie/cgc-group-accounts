<?php
$group_id = cgc_group_accounts()->members->get_group_id();
$role     = cgc_group_accounts()->members->get_role();
?>

<h3>Manage Group Account</h3>
<p>Your group account control panel. Add, remove, and promote group members!</p>

<form method="post" id="add-group-member-form">

	<p><strong>Add a member to your group</strong></p>

	<p>Enter the email address of a user account to add to the group.</p>

	<input type="text" name="user_email" id="user_email" autocomplete="off" />

	<input type="hidden" name="group" id="group" value="<?php echo absint( $group_id ); ?>" />
	<input type="hidden" name="cgcg-action" value="add-member" />

	<input type="submit" value="Add Member" />

</form>


<table class="rcp-table" id="rcp-group-dashboard">
	<thead>
		<tr>
			<th>Group Name</th>
			<th>Your Role</th>
			<th>Group Members</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo cgc_group_accounts()->members->get_group_name(); ?></td>
			<td><?php echo $role; ?></td>
			<td><?php echo cgc_group_accounts()->groups->get_member_count( $group_id ); ?></td>
		</tr>
	</tbody>
</table>
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