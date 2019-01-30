import 'underscore';
import 'backbone';

export default Backbone.Model.extend({
  idAttribute: "id",
  url: ajaxurl+'?action=toplist',
  defaults: {
    name: '',
    description: ''
  },
});
