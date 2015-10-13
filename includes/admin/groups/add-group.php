<div class="wrap">

	<h2>New Group</h2>
	
	<form method="post">

		<table class="form-table">

			<tr class="form-row form-required">

				<th scope="row">
					<label for="name">Name</label>
				</th>

				<td>
					<input type="text" name="name" id="name" class="regular-text" autocomplete="off" />
					<p class="description">The name of this group</p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="description">Description</label>
				</th>

				<td>
					<input type="text" name="description" id="description" class="regular-text" autocomplete="off" />
					<p class="description">The description of this group</p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="user_email">Group Owner</label>
				</th>

				<td>
					<input type="text" name="user_email" id="user_email" />
					<p class="description">Enter the email address of the user account to set as the group owner.</p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="seats">Seats</label>
				</th>

				<td>
					<input type="number" min="1" step="1" name="seats" id="seats" class="regular-text" autocomplete="off" />
					<p class="description">The number of seats for this group</p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="expiration">Expiration</label>
				</th>

				<td>
					<input type="date" min="1" step="1" name="expiration" id="expiration" class="regular-text" autocomplete="off" />
					<p class="description">Set an expiration for this group</p>
				</td>

			</tr>
		</table>

		<input type="hidden" name="user_id" id="user_id" value="" />
		<input type="hidden" name="cgcg-action" value="add-group" />

		<?php submit_button( 'Add Group' ); ?>

	</form>

</div>
