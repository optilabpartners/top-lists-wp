<!-- src/templates/metabox.templ.php  -->
<h1>TopLists</h1>
<br>
<div class="wrap container-fluid">
	<div class="row">
		<div class="col-sm-6 col-md-8">
			<table class="table wp-list-table widefat table-hover table-responsive">
				<thead>
					<tr>
						<th>Shortcode</th>
						<th>Name</th>
						<th>Description</th>
						<th>Action</th>
					</tr>
					<tr class="form">
						<td><h4>Add a TopList</h4></td>
						<td><div class="form-group>"><input type="text" class="form-control name-input" id="newNameInput" placeholder=" A TopList name" required ></div></td>
						<td><div class="form-group>"><input type="text" class="form-control description-input" id="newDescriptionInput" placeholder=" Description" ></div></td>
						<td><div class="form-group>"><button id="btnAddToplist" class="button button-primary">Add</button></div></td>
					</tr>
				</thead>
				<tbody id="toplist-list-view-template"></tbody>
			</table>
		</div>
		<div class="col-sm-6 col-md-4">
			<table class="wp-list-table widefat striped table-responsive">
			<thead>
				<tr><th>TopList Item <em>(Ranked)</em></th>
				<th>Action</th></tr>
			</thead>
			<tbody class="toplist-items" id="toplist-items-container-template">
				<tr><td colspan="2"><div class="alert alert-warning">Select a Toplist to show TopList Items</div></td></tr>
			</tbody>
			</table>
		</div>
	</div>
</div>
<!-- Template -->
<script type="text/template" id="toplist-edit-single-template">
<td></td>
<td><input type="text" class="form-control name-input" id="nameInput" value="<%= name %>"></td>
<td><input type="text" class="form-control description-input" id="descriptionInput" value="<%= description %>"></td>
<td><button id="btnUpdate" class="button button-primary button-small">Update</button> <button id="btnCancelEdit" class="button button-default button-small">Cancel</button></td>
</script>
<!-- End template -->


<!-- Template -->
<script type="text/template" id="toplist-list-single-template">
<td><button id="btnSelect" class="button button-primary button-small">Select</button>&nbsp;<span class="name"><code>[toplist id="<%= id %>"]</code></span></td>
<td><span class="name"><%= name %></span></td>
<td><span class="description"><%= description %></span></td>
<td><button id="btnEdit" class="button button-default button-small">Edit</button> <button id="btnDelete" class="button button-default button-small">Delete</button></td>
</script>
<!-- End template -->


<!-- Template -->
<script type="text/template" id="toplist-items-list-template">
<tr>
<td><span class="name"><%= post_title %></span></td>
<td><a id="btnEdit" href="/wp-admin/post.php?post=<%= ID %>&amp;action=edit" class="button button-default button-small">Edit</a></td>
</tr>
</script>
<!-- End template -->