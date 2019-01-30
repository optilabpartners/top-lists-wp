import 'underscore';
import 'backbone';
import TopListModel from '../models/TopListModel';

export default Backbone.Collection.extend({
  model: TopListModel,
  url: ajaxurl+'?action=toplists',
});
