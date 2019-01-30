import 'underscore';
import 'backbone';
import jQuery from 'jquery';
import 'jquery-ui';
import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/disable-selection';
import 'jquery-ui/ui/widgets/draggable';
import 'jquery-ui/ui/widgets/droppable';
import 'jquery-ui/ui/widgets/selectable';
import TopListItemListView from './TopListItemListView';

let $ = jQuery;

export default Backbone.View.extend({
  tagName: 'tr',
    // Get the template from the DOM
    initialize: function(model) {
     this.model = model;
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
      this.model.set({
        'name': this.$('#nameInput').val(),
        'description': this.$('#descriptionInput').val()
      });
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
      $('#toplist-items-container-template').html('<tr><td colspan="3"><div class="alert alert-warning">No Items found for this toplist</div></td></tr>');
      $(e.currentTarget).parent().parent('tr').addClass('info').siblings().removeClass('info');
      this.child = new TopListItemListView(this.model.id);
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
