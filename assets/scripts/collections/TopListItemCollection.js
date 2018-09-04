TLAApp.TopListItemCollection = Backbone.Collection.extend({
  model: TLAApp.TopListItemModel,
  url: ajaxurl+'?action=toplist_items',

  comparator: function(model) {
    return model.get('rank');
  }
});
