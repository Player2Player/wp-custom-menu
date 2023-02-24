<?php if ($data['location']['locationCategoryId'] == 2 && $data['location']['slug'] != 'frisco') { ?>
    <div style="height: auto; width: 100%" >
        <img style="width: 100%; height: auto" decoding="async"
             src="https://player2player.com/wp-content/uploads/2023/02/thumbnail_webbanner-addingcoaches-1.png" alt="" >
    </div>
<?php } ?>
<div style="margin-top: -80px" class="et_pb_column et_pb_column_4_4 et_pb_column_0  et_pb_css_mix_blend_mode_passthrough et-last-child">
  <div class="et_pb_module et_pb_text et_pb_text_0  et_pb_text_align_left et_pb_bg_layout_light">
    <div class="et_pb_text_inner">
      <h1 style="text-align: center" class="entry-title main_title">
          BOOK A LESSON: <?php echo $data['location']['name'] ?>
      </h1>
    </div>
  </div>
  <!-- .et_pb_text -->
  <div class="et_pb_module et_pb_text et_pb_text_1  et_pb_text_align_left et_pb_bg_layout_light">
    <div class="et_pb_text_inner">
      <?php echo do_shortcode("[ameliacatalogbooking location={$data['location']['id']}]") ?>
    </div>
  </div>
  <!-- .et_pb_text -->
</div>
<!-- .et_pb_column -->
<script>
    jQuery(document).ready(function () {
        window.onscroll = function(event) {
          event.stopPropagation();
          jQuery('#main-header').addClass('fixed-header et-fixed-header');
        };
        window.scrollTo({ top: 10, behavior: 'smooth'})
    });
</script>
