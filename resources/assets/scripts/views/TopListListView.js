(function($) {
  TLAApp.TopListListView = Backbone.View.extend({
    el: $('#toplist-list-view-template'),
    initialize: function() {
      this.collection = new TLAApp.TopListCollection();
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
        self.$el.append((new TLAApp.TopListSingleView(toplist)).render().$el);
      });
      return this;
    }
  });
}(jQuery));
