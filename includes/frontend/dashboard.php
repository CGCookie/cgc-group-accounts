<?php
$group_id = cgc_group_accounts()->members->get_group_id();
$role     = cgc_group_accounts()->members->get_role();
?>

<h3>Manage Group Account</h3>
<p>Your group account control panel. Add, remove, and promote group members!</p>

<ul id="group-tabs">
	<li class="group-tab-active"><a href="#group-tab-members">Members</a></li>
	<li><a href="#group-tab-settings">Group Settings</a></li>
	<!--<li><a href="#group-tab-billing">Billing</a></li>-->
	<li><a href="#group-tab-support">Support</a></li>
</ul>

<div class="group-tab-content" id="group-tab-members">

	<?php if( 'owner' === $role || 'admin' === $role ) : ?>

		<form method="post" id="add-group-member-form">

			<p><strong>Add a member to your group</strong></p>

			<p>Enter the email address of a user account to add to the group. This email must be already registered with a user account.</p>

			<p>
				<input type="text" name="user_email" id="user_email" autocomplete="off" />
				<input type="hidden" name="group" id="group" value="<?php echo absint( $group_id ); ?>" />
				<input type="hidden" name="cgcg-action" value="add-member" />
				<a href="#" data-reveal-id="group-add-member-confirmation" id="group-add-member-submit">Add Member</a>

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

		<table class="rcp-table" id="rcp-group-dashboard-members">
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
					<td class="member-number"><?php echo $i; ?></td>
					<td class="member-avatar">
						<?php echo get_avatar( $member->user_id ); ?>
					</td>
					<td class="member-name"><?php echo $user_data->display_name; ?></td>
					<td><?php echo $member->role; ?></td>
					<td>
						<?php if( 'owner' != $member->role ) : ?>
							<div id="group-remove-member-confirmation-<?php echo $member->user_id; ?>" class="reveal-modal">
								<h4>Confirm member removal</h4>
								<div class="group-member-gravatar">
									<?php echo get_avatar( $member->user_id ); ?>
									<span class="member-name"><?php echo $user_data->display_name; ?></span>
									<span class="member-email"><?php echo $user_data->user_email; ?></span>
								</div>
								<p><strong>Confirm removal of this member</strong></p>
								<p>By removing this user, you will be removing all group access for this member</p>

								<a href="#" class="close-modal">Nah, nevermind</a>
								<a href="<?php echo esc_url( home_url( 'index.php?cgcg-action=remove-member&group=' . $member->group_id . '&member=' . $member->user_id ) ); ?>">Remove Member</a>

								<a class="close-reveal-modal">&#215;</a>
							</div>
							<a href="#" data-reveal-id="group-remove-member-confirmation-<?php echo $member->user_id; ?>">Remove from Group</a>&nbsp;|&nbsp;
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

</div>

<div class="group-tab-content" id="group-tab-settings" style="display:none;">
	
	<form method="post" id="group-settings-form">

		<p>
			<label for="group_name">The name of your group.</label>
			<input type="text" name="name" id="group_name"/>
		</p>

		<p>
			<label for="group_description">About your group.</label>
			<textarea name="description" id="group_description"></textarea>
		</p>

		<p>
			<input type="hidden" name="group" value="<?php echo absint( $group_id ); ?>" />
			<input type="hidden" name="cgcg-action" value="edit-group" />
			<input type="submit" value="Update Group" />
		</p>
	</form>

</div>

<div class="group-tab-content" id="group-tab-billing" style="display:none;">
	<p>Coming soon</p>
</div>

<div class="group-tab-content" id="group-tab-support" style="display:none;">
	<p>For assistance with your group account, please email us: <a href="mailto:support@cgcookie.com">support@cgcookie.com</a></p>
</div>