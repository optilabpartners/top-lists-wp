import 'underscore';
import 'backbone';
import TopListItemModel from '../models/TopListItemModel';

export default Backbone.Collection.extend({
  model: TopListItemModel,
  url: ajaxurl+'?action=toplist_items',

  comparator: function(model) {
    return model.get('rank');
  }
});
