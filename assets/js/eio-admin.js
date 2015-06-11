var element = null;
jQuery(document).ready(function() {
  jQuery('.eio-extracted-data-link').click(function() {
    var index = jQuery(this).attr('rel');
    tb_show('', '?TB_inline=true&width=900&height=600&inlineId=eio-extracted-data-' + index);
    return false;
  });
});