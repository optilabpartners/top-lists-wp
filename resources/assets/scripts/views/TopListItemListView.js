import 'underscore';
import 'backbone';
import jQuery from 'jquery';
import 'jquery-ui';
import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/disable-selection';
import 'jquery-ui/ui/widgets/draggable';
import 'jquery-ui/ui/widgets/droppable';
import 'jquery-ui/ui/widgets/selectable';
import TopListItemCollection from '../collections/TopListItemCollection';
import TopListItemView from './TopListItemView';

let $ = jQuery;

export default Backbone.View.extend({

  rowsSelected: [],
  collection: new TopListItemCollection(),
  el: $('#toplist-items-container-template'),
  events: {
        'update-sort' : 'updateSort'
  },
  initialize: function(toplistId) {
    this.listenTo(this.collection, 'change', this.render);
    this.listenTo(this.collection, 'sync', this.render);
    var self = this;
    this.listenToOnce(this.collection, 'sync', function() {
      self.makeSortable();
      //self.getRowOptions();
      //self.changeRowLevel();
    });

    this.collection.fetch({
      beforeSend: function(xhr) {
        xhr.setRequestHeader('ToplistID', toplistId);
      },
      success: function(response) {
        _.each(response.toJSON(), function(item){
          console.log('Loaded item ' + item.ID);
        });
      },
      error: function(e) {
        console.log('Error ' + e);
      }
    });
  },
  updateSort: function(event, model, position) {
        this.collection.remove(model);
        this.collection.each(function (model, index) {
            var ordinal = index;
            if (index >= position) {
                ordinal += 1;
            }
            model.set('rank', ordinal);
        });

        model.set('rank', position);
        this.collection.add(model, {at: position});
        Backbone.sync('update', this.collection, { contentType: 'application/json', data: JSON.stringify(this.collection) });
        return this.render();
    },

    render: function() {
        this.$el.children().remove();
        this.rowsSelected = [];
        this.collection.each(this.appendModelView, this);
        return this;
    },

    makeSortable: function() {
      $( ".sortable" ).sortable({
        // consider using update instead of stop
        update: function(event, ui) {
          console.log(ui.item);
          ui.item.trigger('drop', ui.item.index());
        }
      });
      $( ".sortable" ).disableSelection();
      $( ".sortable tr td" ).css('cursor', 'move');
    },

    getRowOptions: function() {
      $.getJSON( ajaxurl+'?action=row_templates', function(data) {
        $("#rowLevel").empty().hide();
        $("#rowLevel").append('<option value="0">Row Template</option>');
        $.each(data, function(){
            $("#rowLevel").append('<option value="'+ this.id +'">'+ this.name +'</option>');
        });
      });
    },

    changeRowLevel: function() {
      var self = this;
      $('#rowLevel').on('change', function() {
        const $that = $(this);

        if ( parseInt($(this).val()) === 0 ||  self.rowsSelected.length === 0) {
          return false;
        }

        var data = [];
        const rowsSelected = self.rowsSelected;
        for (var i = 0; i < rowsSelected.length; i++) {

          const level = $(this).children('[value="' +  $that.val() + '"]').text();
          $("#toplist-items-container-template tr:eq(" + rowsSelected[i] + ") td:nth-child(3)").html(level);
          const toplist_id = $("#toplist-items-container-template tr:eq(" + rowsSelected[i] + ")").data("toplist");
          data.push({ "toplist_id": toplist_id, "row_number": rowsSelected[i], "template_id": $that.val()});
          let model = self.collection.at(rowsSelected[i]);
          model.set('template', level);
        }
        $.ajax({
          url: ajaxurl+'?action=toplist_rows_templates',
          method: 'PUT',
          data: JSON.stringify(data),
          dataType:"json",
        });
      }).hide();
    },

    appendModelView: function(model) {
        var view = new TopListItemView({model: model, parent: this});
        var el = view.render().el;
        this.$el.append(el);
    },

});
