
TLAApp.TopListCollection = Backbone.Collection.extend({
  model: TLAApp.TopListModel,
  url: ajaxurl+'?action=toplists',
});
