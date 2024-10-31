jQuery(function($) {

jQuery('#select-sc-toc_1st_tag').change(function(){
  var val = jQuery(this).val();
  var max = Number(val.slice( 1 ));

  jQuery('#select-sc-toc_2nd_tag option').remove();
  jQuery('#select-sc-toc_2nd_tag').append($('<option>').html('指定なし').val(0));
  for (let i = (max+1); i <= 6; i++) {
    tag = 'h' + i;
    jQuery('#select-sc-toc_2nd_tag').append($('<option>').html(tag).val(tag));
  }

});

});
