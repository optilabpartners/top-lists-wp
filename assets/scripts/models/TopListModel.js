var TLAApp = {};

TLAApp.TopListModel = Backbone.Model.extend({
  idAttribute: "id",
  url: ajaxurl+'?action=toplist',
  defaults: {
    name: '',
    description: ''
  },
});
