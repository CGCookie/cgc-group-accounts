<div class="wrap" id="rcp-members-page">

	<h2>Groups</h2>
	<table class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th>Name</th>
				<th>ID</th>
				<th>Owner</th>
				<th>Description</th>
				<th>Members</th>
				<th>Fixed Billing</th>
				<th>Date Created</th>
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
				<th>Fixed Billing</th>
				<th>Date Created</th>
				<th>Actions</th>
			</tr>
		</tfoot>
		<tbody>
		<?php

		$groups = cgc_group_accounts()->groups->get_groups();
		
		if( ! empty( $groups ) ) :
			$i = 1;
			foreach( $groups as $key => $group ) : ?>
				<tr>
					<td><?php echo $group->name; ?></td>
					<td><?php echo $group->group_id; ?></td>
					<td><?php echo $group->owner_id; ?></td>
					<td><?php echo $group->description; ?></td>
					<td><?php echo $group->member_count; ?></td>
					<td><?php echo $group->fixed_billing; ?></td>
					<td><?php echo $group->date_created; ?></td>
					<td></td>
				</tr>
			<?php $i++;
			endforeach;
		else : ?>
			<tr><td colspan="8">No groups found</td></tr>
		<?php endif; ?>
		</tbody>
	</table>
</div><!--end wrap-->