import 'underscore';
import 'backbone';
import jQuery from 'jquery';
import 'jquery-ui';
import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/disable-selection';
import 'jquery-ui/ui/widgets/draggable';
import 'jquery-ui/ui/widgets/droppable';
import 'jquery-ui/ui/widgets/selectable';

let $ = jQuery;

export default Backbone.View.extend({
      tagName: 'tr',
      className: 'ui-state-default',
      template: _.template($('#toplist-items-list-template').html()),
      initialize : function (options) {
        this.parent = options.parent;
      },
      events: {
          'drop' : 'drop',
          'click': 'selectRow'
      },
      drop: function(event, index) {
        this.$el.trigger('update-sort', [this.model, index]);
      },
      selectRow: function(event) {
        const $that = this.$el;
        if( $that.hasClass('row-selected') ) {
          const index = this.parent.rowsSelected.indexOf($that.index());
          if (index > -1) {
              this.parent.rowsSelected.splice(index, 1);
          }
          $that.removeClass('row-selected');
        } else {
          if (event.shiftKey) {
            const $nearestSelected = $that.prevAll('[class*="row-selected"]').first();
            var start = $nearestSelected.index();
            for (var i = start + 1; i < $that.index(); i++) {
              this.parent.rowsSelected.push(i);
              $that.siblings(':eq(' + i + ')').addClass('row-selected');
            }
          }
          this.parent.rowsSelected.push($that.index());
          $that.addClass('row-selected');
        }

        if (this.parent.rowsSelected.length === 0) {
          $('#rowLevel').val(0).hide();
        } else {
          $('#rowLevel').show();
        }
      },
      render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        this.$el.attr('data-toplist', this.model.get("toplist"));
        return this;
      }
  });
