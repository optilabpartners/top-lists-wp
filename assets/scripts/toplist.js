(function($) {

  var tLV = new TLAApp.TopListListView();

  $('#btnAddToplist').on('click', function() {
    if ($('#newNameInput').val() === '') {
      return false;
    }

    var t = new TopListModel({
      name: $('#newNameInput').val(),
      description: $('#newDescriptionInput').val()
    });
    t.save();

    $('#newNameInput, #newDescriptionInput').val('');
    tLV.collection.create(t);

  });

}(jQuery));
