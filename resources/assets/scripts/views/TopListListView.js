import 'underscore';
import 'backbone';
import jQuery from 'jquery';
import 'jquery-ui';
import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/disable-selection';
import 'jquery-ui/ui/widgets/draggable';
import 'jquery-ui/ui/widgets/droppable';
import 'jquery-ui/ui/widgets/selectable';
import TopListSingleView from './TopListSingleView';
import TopListCollection from '../collections/TopListCollection';

let $ = jQuery;

export default Backbone.View.extend({
    el: $('#toplist-list-view-template'),
    initialize: function() {
      this.collection = new TopListCollection();
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
        self.$el.append((new TopListSingleView(toplist)).render().$el);
      });
      return this;
    }
  });
