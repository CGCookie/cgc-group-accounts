<?php $group_id = absint( $_GET['group'] ); ?>
<div class="wrap" id="rcp-members-page">

	<h2>Group Members <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgc-groups&view=add-member&group=' . $group_id ) ); ?>" class="add-new-h2">Add Member</a></h2>

	<table class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th>Name</th>
				<th>ID</th>
				<th>Group</th>
				<th>Role</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Name</th>
				<th>ID</th>
				<th>Group</th>
				<th>Role</th>
				<th>Actions</th>
			</tr>
		</tfoot>
		<tbody>
		<?php

		$members = cgc_group_accounts()->groups->get_members( $group_id );
		
		if( ! empty( $members ) ) :
			$i = 1;
			foreach( $members as $key => $member ) : ?>

				<?php
				$user_data = get_userdata( $member->user_id );
				if( ! $user_data ) {
					continue;
				}
				?>

				<tr<?php echo $i & 1 ? ' class="alternate"' : ''; ?>>
					<td><?php echo $user_data->display_name; ?></td>
					<td><?php echo $member->user_id; ?></td>
					<td><?php echo $member->group_id; ?></td>
					<td><?php echo $member->role; ?></td>
					<td>
						<?php if( 'owner' != $member->role ) : ?>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=cgc-groups&cgcg-action=remove-member&group=' . $member->group_id . '&member=' . $member->user_id ) ); ?>">Remove from Group</a>&nbsp;|&nbsp;
							<?php if( 'admin' == $member->role ) : ?>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=cgc-groups&cgcg-action=make-member&group=' . $member->group_id . '&member=' . $member->user_id ) ); ?>">Set as Member</a>
							<?php else : ?>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=cgc-groups&cgcg-action=make-admin&group=' . $member->group_id . '&member=' . $member->user_id ) ); ?>">Set as Admin</a>
							<?php endif; ?>
						<?php endif; ?>
					</td>
				</tr>
			<?php $i++;
			endforeach;
		else : ?>
			<tr><td colspan="5">No members in this group</td></tr>
		<?php endif; ?>
		</tbody>
	</table>
</div><!--end wrap-->