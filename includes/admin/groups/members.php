<?php $group_id = absint( $_GET['group'] ); ?>
<div class="wrap" id="rcp-members-page">

	<h2>Group Members</h2>
	<table class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th>Name</th>
				<th>ID</th>
				<th>Group</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Name</th>
				<th>ID</th>
				<th>Group</th>
				<th>Actions</th>
			</tr>
		</tfoot>
		<tbody>
		<?php

		$members = cgc_group_accounts()->groups->get_members( $group_id );
		
		if( ! empty( $members ) ) :
			$i = 1;
			foreach( $members as $key => $member ) : ?>
				<tr>
					<td></td>
					<td><?php echo $member->user_id; ?></td>
					<td><?php echo $member->group_id; ?></td>
					<td></td>
					<td></td>
				</tr>
			<?php $i++;
			endforeach;
		else : ?>
			<tr><td colspan="5">No members in this group</td></tr>
		<?php endif; ?>
		</tbody>
	</table>
</div><!--end wrap-->