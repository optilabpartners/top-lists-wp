
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

	TopList.Item = Backbone.Model.extend({
		idAttribute: "ID",
		url: ajaxurl+'?action=toplist_item',
		
	});

	TopList.ItemCollection = Backbone.Collection.extend({
		model: TopList.Item,
		url: ajaxurl+'?action=toplist_items',

		comparator: function(model) {
			return model.get('rank');
		}
	});

	var toplistItems = new TopList.ItemCollection();


/*************************************************** TopList SingleView ******************************************************/

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


/*************************************************** TopList SingleEditView ******************************************************/

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




/*************************************************** TopList ListView ******************************************************/

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


	TopList.ItemView = Backbone.View.extend({
	    tagName: 'tr',
	    className: 'ui-state-default',
	    template: _.template($('#toplist-items-list-template').html()),
	    events: {
	        'drop' : 'drop'
	    },
	    drop: function(event, index) {
	        this.$el.trigger('update-sort', [this.model, index]);
	    },        
	    render: function() {
	    	this.$el.html(this.template(this.model.toJSON()));
	   		return this;
	    }
	});


/*************************************************** TopList ItemsView ******************************************************/


	TopList.ItemsView = Backbone.View.extend({

		collection: toplistItems,
		el: $('#toplist-items-container-template'),
		events: {
        	'update-sort' : 'updateSort'
    	},
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
		updateSort: function(event, model, position) {            
	        this.collection.remove(model);

	        this.collection.each(function (model, index) {
	            var ordinal = index;
	            if (index >= position) {
	                ordinal += 1;
	            }
	            model.set('rank', ordinal);
	        });            

	        model.set('rank', position);
	        console.log(model.toJSON());
	        this.collection.add(model, {at: position});

	        Backbone.sync('update', this.collection, { contentType: 'application/json', data: JSON.stringify(this.collection) });
	        return this.render();
	    },

	    render: function() {
	        this.$el.children().remove();
	        this.collection.each(this.appendModelView, this);
	        return this;
	    },    
	    appendModelView: function(model) {
	        var el = new TopList.ItemView({model: model}).render().el;
	        this.$el.append(el);
	    },

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

	$( ".sortable" ).sortable({
        // consider using update instead of stop
        stop: function(event, ui) {
            ui.item.trigger('drop', ui.item.index());
        }
    });
    $( ".sortable" ).disableSelection();
    $( ".sortable tr td" ).css('cursor', 'move');

}(jQuery));