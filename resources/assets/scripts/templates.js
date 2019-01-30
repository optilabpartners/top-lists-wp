import 'jquery-ui';

(function($) {
  // Toplist Model
  var TopListTemplate = Backbone.Model.extend({
    idAttribute: 'id',
    url: ajaxurl+'?action=toplist_template',
    defaults: {
      name: '',
      options: '',
      fields: ''
    },
  });

  // Toplist Collection

  TopListTemplate.Collection = Backbone.Collection.extend({
    model: TopListTemplate,
    url: ajaxurl+'?action=toplist_templates'
  });

  var toplistTemplates = new TopListTemplate.Collection();


/*************************************************** TopList SingleView ******************************************************/

  TopListTemplate.SingleView = Backbone.View.extend({
    model: toplistTemplates.models,
    tagName: 'tr',
      // Get the template from the DOM
      initialize: function() {
        this.template = _.template( $('#toplist-template-list-single-template').html() );
      this.model.on('change', this.render, this);
      this.model.on('remove', this.render, this);
      // this.model.on('add', this.render, this);
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
        this.template = _.template( $('#toplist-template-edit-single-template').html() );
        return this.render();
      },
      cancelEdit: function() {
        this.template = _.template( $('#toplist-template-list-single-template').html() );
        return this.render();
      },
      update: function(e) {
        this.model.set('name', this.$('#updateNameInput').val());

        var toplistOptions = {};
        if ($('#update_highlight_first:checked').length > 0) {
        toplistOptions.highlight_first = $('#update_highlight_first').val();
        }

      var $toplistFieldInputs = $('input[name*="update_fields"]:checked');
      var toplistFields = {};
      $toplistFieldInputs.each(function(i, elem) {
        var id = $(elem).attr('id');
        toplistFields[id] = $(elem).val();
      });
      this.model.set('options', toplistOptions);
      this.model.set('fields', toplistFields);
        this.save(e);
      },
      delete: function() {
        var modelJSON = this.model.toJSON();
        this.model.destroy({
          headers: modelJSON
        });
        this.remove();

      },

      save: function(e) {
        e.preventDefault();
            $(e.target).text( 'Saving...' );
            this.model.save();
            this.template = _.template( $('#toplist-template-list-single-template').html() );
            return this.render();
      },
      render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        return this;
      }
  });


/*************************************************** TopList SingleEditView ******************************************************/

  TopListTemplate.SingleEditView = Backbone.View.extend({
    model: toplistTemplates.models,
    tagName: 'tr',
      // Get the template from the DOM
      initialize: function() {
        this.template = _.template( $('#toplist-templates-edit-single-template').html() );
      },
      render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        return this;
      }
  });

/*************************************************** TopList ListView ******************************************************/

  TopListTemplate.ListView = Backbone.View.extend({
    // model: toplists,
    collection: toplistTemplates,
    el: $('#toplist-templates-list-view-template'),
    initialize: function() {
      // this.collection.on('add', this.render, this);
      this.listenTo(this.collection, 'add', this.render);
      this.listenTo(this.collection, 'change', this.render);
      this.collection.fetch({
        success: function(response) {
          _.each(response.toJSON(), function(toplist){
            console.log('Loaded Toplist Template ' + toplist.id);
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
        self.$el.append((new TopListTemplate.SingleView({model: toplist})).render().$el);
      });
      return this;
    }
  });


  new TopListTemplate.ListView();

  $('#btnAddToplistTemplate').on('click', function() {
    if ($('#newNameInput').val() === '') {
      return false;
    }
    var toplistOptions = {};
      if ($('#highlight_first:checked').length > 0) {
      toplistOptions.highlight_first = $('#highlight_first').val();
      }

    var $toplistFieldInputs = $('input[name*="field"]:checked');
    var toplistFields = {};
    if ($toplistFieldInputs.length === 0 && $('#highlight_first:checked').length == 0) {
      alert ('Atleast one field must be selected');
      return;
    }
    $toplistFieldInputs.each(function(i, elem) {
      var id = $(elem).attr('id');
      toplistFields[id] = $(elem).val();
    });

    var t = new TopListTemplate({
      name: $('#newNameInput').val(),
      options: toplistOptions,
      fields: toplistFields
    });
    toplistTemplates.create(t);
    $('#newNameInput').val('');
  });

}(jQuery));
