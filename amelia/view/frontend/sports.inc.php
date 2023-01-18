<div class="et_pb_column et_pb_column_4_4 et_pb_column_0  et_pb_css_mix_blend_mode_passthrough et-last-child">
  <div class="et_pb_module et_pb_text et_pb_text_0  et_pb_text_align_left et_pb_bg_layout_light">
    <div class="et_pb_text_inner">
      <h1 class="entry-title main_title">BOOK A LESSON: <?php echo $data['location']['name'] ?></h1>
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
