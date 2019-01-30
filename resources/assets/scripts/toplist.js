import "underscore";
import "backbone";
import jQuery from "jquery";
import "jquery-ui";
import "jquery-ui/ui/widgets/sortable";
import "jquery-ui/ui/disable-selection";
import "jquery-ui/ui/widgets/draggable";
import "jquery-ui/ui/widgets/droppable";
import "jquery-ui/ui/widgets/selectable";
import TopListListView from "./views/TopListListView";
import TopListModel from "./models/TopListModel";

(function($) {
  var tLV = new TopListListView();

  $("#btnAddToplist").on("click", function() {
    if ($("#newNameInput").val() === "") {
      return false;
    }

    var t = new TopListModel({
      name: $("#newNameInput").val(),
      description: $("#newDescriptionInput").val(),
    });
    t.save();

    $("#newNameInput, #newDescriptionInput").val("");
    tLV.collection.create(t);
  });
})(jQuery);
