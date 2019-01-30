import 'underscore';
import 'backbone';
import jQuery from 'jquery';
import 'jquery-ui';
import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/disable-selection';
import 'jquery-ui/ui/widgets/draggable';
import 'jquery-ui/ui/widgets/droppable';
import 'jquery-ui/ui/widgets/selectable';

let $ = jQuery;

export default Backbone.View.extend({
  tagName: 'tr',
    // Get the template from the DOM
    initialize: function(model) {
      this.model = model;
      this.template = _.template( $('#toplist-edit-single-template').html() );
    },
    render: function() {
      this.$el.html(this.template(this.model.toJSON()));
      return this;
    }
});
