<div class="wrap" id="rcp-members-page">

	<h2>Group <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgc-groups&view=add-group' ) ); ?>" class="add-new-h2">Add Group</a></h2>
	<table class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th>Name</th>
				<th>ID</th>
				<th>Owner</th>
				<th>Description</th>
				<th>Members</th>
				<th>Seats</th>
				<th>Date Created</th>
				<th>Date Expires</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Name</th>
				<th>ID</th>
				<th>Owner</th>
				<th>Description</th>
				<th>Members</th>
				<th>Seats</th>
				<th>Date Created</th>
				<th>Date Expires</th>
				<th>Actions</th>
			</tr>
		</tfoot>
		<tbody>
		<?php

		$groups = cgc_group_accounts()->groups->get_groups();
		
		if( ! empty( $groups ) ) :
			$i = 1;
			foreach( $groups as $key => $group ) : ?>
				<tr<?php echo $i & 1 ? ' class="alternate"' : ''; ?>>
					<td><?php echo stripslashes( $group->name ); ?></td>
					<td><?php echo $group->group_id; ?></td>
					<td><?php echo $group->owner_id; ?></td>
					<td><?php echo stripslashes( $group->description ); ?></td>
					<td><?php echo absint( $group->member_count ); ?></td>
					<td><?php echo absint( $group->seats ); ?></td>
					<td><?php echo $group->date_created; ?></td>
					<td><?php echo $group->expiration; ?></td>
					<td>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=cgc-groups&view=edit&group=' . $group->group_id ) ); ?>">Edit</a>&nbsp;|&nbsp;
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=cgc-groups&view=view-members&group=' . $group->group_id ) ); ?>">Members</a>&nbsp;|&nbsp;
						<span class="trash">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=cgc-groups&cgcg-action=delete-group&group=' . $group->group_id ) ); ?>" class="submitdelete" style="color:#a00">Delete</a>
						</span>
					</td>
				</tr>
			<?php $i++;
			endforeach;
		else : ?>
			<tr><td colspan="8">No groups found</td></tr>
		<?php endif; ?>
		</tbody>
	</table>
</div><!--end wrap-->