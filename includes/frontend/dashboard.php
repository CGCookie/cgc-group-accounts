<?php
$group_id = cgc_group_accounts()->members->get_group_id();
$role     = cgc_group_accounts()->members->get_role();
?>
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
<script>
jQuery(document).ready(function($) {
// ajax user search
	$('.cgc-user-search').keyup(function() {
		var user_search = $(this).val();
		$('.cgc-ajax').show();
		data = {
			action: 'cgc_search_users',
			user_name: user_search
		};

		$.ajax({
			type: "POST",
			data: data,
			dataType: "json",
			url: ajaxurl,
			success: function (search_response) {

				$('.cgc-ajax').hide();

				$('#cgc_user_search_results').html('');

				$(search_response.results).appendTo('#cgc_user_search_results');
			}
		});
	});
	$('body').on('click.rcpSelectUser', '#cgc_user_search_results a', function(e) {
		e.preventDefault();
		var login = $(this).data('login'), id = $(this).data('id');
		$('#user_name').val(login);
		$('#user_id').val(id);
		$('#cgc_user_search_results').html('');
	});
});
</script>
<style>
.cgc-ajax-search-wrap {
	position: relative;
}
.cgc-ajax {
	position: absolute;
	right: 8px;
	top: 1px;
}
#cgc_user_search_results {
	position: absolute;
}
#cgc_user_search_results p,
#cgc_user_search_results ul {
	padding: 10px 10px 4px;
	margin: 0;
	background: #f0f0f0;
	border: 1px solid #DFDFDF;
	width: 300px;
	max-height: 200px;
	overflow-y: scroll;
}
</style>
<div class="wrap">

	<h2>New Group Member</h2>
	
	<form method="post">
	
		<label for="user_name">User</label>
		<span class="cgc-ajax-search-wrap">
			<input type="text" name="user_name" id="user_name" class="cgc-user-search" autocomplete="off" />
			<img class="cgc-ajax waiting" src="<?php echo admin_url('images/wpspin_light.gif'); ?>" style="display: none;"/>
		</span>
		<div id="cgc_user_search_results"></div>
		<p class="description">Begin typing the name of the user to perform a search.</p>

		<input type="hidden" name="user_id" id="user_id" value="" />
		<input type="hidden" name="group" id="group" value="<?php echo absint( $group_id ); ?>" />
		<input type="hidden" name="cgcg-action" value="add-member" />

		<input type="submit" value="Add Member" />

	</form>

</div>
<?php endif; ?>