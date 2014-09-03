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
					<label for="description">description</label>
				</th>

				<td>
					<input type="text" name="description" id="description" class="regular-text" autocomplete="off" />
					<p class="description">The description of this group</p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="user_name">Group Owner</label>
				</th>

				<td>
					<span class="cgc-ajax-search-wrap">
						<input type="text" name="user_name" id="user_name" class="cgc-user-search" autocomplete="off" />
						<img class="cgc-ajax waiting" src="<?php echo admin_url('images/wpspin_light.gif'); ?>" style="display: none;"/>
					</span>
					<div id="cgc_user_search_results"></div>
					<p class="description"><?php _e( 'Begin typing the name of the user to perform a search.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

		</table>

		<input type="hidden" name="user_id" id="user_id" value="" />
		<input type="hidden" name="cgcg-action" value="add-group" />

		<?php submit_button( 'Add Group' ); ?>

	</form>

</div>
