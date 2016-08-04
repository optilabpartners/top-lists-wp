
(function($) {
	// Toplist Model
	var TopList = Backbone.Model.extend({
		idAttribute: "id",
		url: ajaxurl+'?action=toplist',
		defaults: {
			name: '',
			description: ''
		},
	});

	// Toplist Collection

	TopList.Collection = Backbone.Collection.extend({
		model: TopList,
		url: ajaxurl+'?action=toplists'
	});

	var toplists = new TopList.Collection();



	TopList.ItemCollection = Backbone.Collection.extend({
		idAttribute: "ID",
		url: ajaxurl+'?action=toplist_items',
	});

	var toplistItems = new TopList.ItemCollection();

	TopList.SingleView = Backbone.View.extend({
		model: toplists.models,
		tagName: 'tr',
	    // Get the template from the DOM
	    initialize: function() {
	    	this.template = _.template( $('#toplist-list-single-template').html() );
			this.model.on('change', this.render, this);
			this.model.on('remove', this.render, this);
			// this.listenTo(this.model, 'destroy', this.remove);
	    },
	    events: {
	    	'click #btnEdit': 'edit',
	    	'click #btnCancelEdit': 'cancelEdit',
	    	'click #btnUpdate': 'update',
	    	'click #btnDelete': 'delete',
	    	'click #btnSelect': 'select'
	    },
	    edit: function() {
	    	this.template = _.template( $('#toplist-edit-single-template').html() );
	    	return this.render();
	    },
	    cancelEdit: function() {
	    	this.template = _.template( $('#toplist-list-single-template').html() );
	    	return this.render();
	    },
	    update: function(e) {
	    	this.model.set('name', this.$('#nameInput').val());
	    	this.model.set('description', this.$('#descriptionInput').val());
	    	this.save(e);
	    },
	    delete: function() {
	    	var modelJSON = this.model.toJSON();
	    	this.model.destroy({
	    		headers: modelJSON
	    	});
	    	this.remove();

	    },

	    select: function(e) {
	    	$('#toplist-items-container-template').html('<tr><td colspan="2"><div class="alert alert-warning">No Items found for this toplist</div></td></tr>');
	    	$(e.currentTarget).parent().parent('tr').addClass('info').siblings().removeClass('info');
	    	var self = this;
	    	toplistItems.fetch({
	    		beforeSend: function(xhr) {
	    			xhr.setRequestHeader('ToplistID', self.model.id);
	    		}
	    	});
	    },
	    save: function(e) {
	    	e.preventDefault();
            $(e.target).text( 'Saving...' );
            this.model.save();
            this.template = _.template( $('#toplist-list-single-template').html() );
            return this.render();
	    },
	    render: function() {
	    	this.$el.html(this.template(this.model.toJSON()));
	    	return this;
	    }
	});

	TopList.SingleEditView = Backbone.View.extend({
		model: toplists.models,
		tagName: 'tr',
	    // Get the template from the DOM
	    initialize: function() {
	    	this.template = _.template( $('#toplist-edit-single-template').html() );
	    },
	    render: function() {
	    	this.$el.html(this.template(this.model.toJSON()));
	    	return this;
	    }
	});



	// Views List of toplist

	TopList.ListView = Backbone.View.extend({
		// model: toplists,
		collection: toplists,
		el: $('#toplist-list-view-template'),
		initialize: function() {
			// this.collection.on('add', this.render, this);
			this.listenTo(this.collection, 'add', this.render);
			this.listenTo(this.collection, 'change', this.render);
			this.collection.fetch({
				success: function(response) {
					_.each(response.toJSON(), function(toplist){
						console.log('Loaded toplist ' + toplist.id);
					});
				},
				error: function(e) {
					console.log('Error ' + e);
				}
			});
		},
		render: function() {
			var self = this;
			this.$el.html('');
			_.each(this.collection.toArray(), function(toplist) {
				self.$el.append((new TopList.SingleView({model: toplist})).render().$el);
			});
			return this;
		}
	});

	TopList.ItemsView = Backbone.View.extend({
		collection: toplistItems,
		el: $('#toplist-items-container-template'),
		initialize: function() {
			this.listenTo(this.collection, 'add', this.render);
			this.listenTo(this.collection, 'change', this.render);
			this.collection.fetch({
				success: function(response) {
					_.each(response.toJSON(), function(item){
						console.log('Loaded item ' + item.ID);
					});
				},
				error: function(e) {
					console.log('Error ' + e);
				}
			});
		},
		render: function() {
			var self = this;
			this.$el.html('');
			_.each(this.collection.toArray(), function(model) {
				self.$el.append(self.addOne(model));
			});
			return this;
		},

		addOne: function(model) {
			var modelTemplate = _.template($('#toplist-items-list-template').html());
			return modelTemplate(model.toJSON());
		}
	});

	var tLV = new TopList.ListView();
	var tIV = new TopList.ItemsView();

	$('#btnAddToplist').on('click', function() {
		if ($('#newNameInput').val() === '') {
			return false;
		}
		var t = new TopList({
			name: $('#newNameInput').val(),
			description: $('#newDescriptionInput').val()
		});
		t.save();
		$('#newNameInput, #newDescriptionInput').val('');
		toplists.create(t);

	});


}(jQuery));