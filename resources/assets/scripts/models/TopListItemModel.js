import 'underscore';
import 'backbone';

export default Backbone.Model.extend({
  idAttribute: "ID",
  url: ajaxurl+'?action=toplist_item',

});
