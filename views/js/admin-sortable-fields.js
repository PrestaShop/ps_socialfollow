$(function () {
  var $wrapper = $('#content .form-wrapper');

  if (!$wrapper.length) return;

  $wrapper.find('> .form-group').each(function () {
    if (!$(this).find('.drag-handle').length) {
      $(this).prepend('<div class="drag-handle" title="">≡</div>');
    }
  });

  $wrapper.sortable({
    items: '> .form-group',
    handle: '.drag-handle',
    axis: 'y',
    tolerance: 'pointer',
    cancel: 'input,textarea,select,button,a,.dropdown-menu',
    update: function () {
      var order = [];

      $wrapper.find('> .form-group').each(function () {
        var $input = $(this).find('input[type="text"][id]').first();
        if (!$input.length) return;

        var id = $input.attr('id');
        var base = id.replace(/_\d+$/, '');
        order.push(base);
      });

      $('input[name="BLOCKSOCIAL_FIELDS_ORDER"]').val(order.join(','));
    }
  });

  $wrapper.trigger('sortupdate');
});
