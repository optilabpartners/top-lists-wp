(function($) {
  TLAApp.TopListSingleEditView = Backbone.View.extend({
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
}(jQuery));
