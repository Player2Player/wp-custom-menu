<div class="et_pb_column et_pb_column_4_4 et_pb_column_1  et_pb_css_mix_blend_mode_passthrough et-last-child">
  <div class="et_pb_module et_pb_text et_pb_text_1  et_pb_text_align_center et_pb_bg_layout_light">
    <div class="et_pb_text_inner">
      <h1>
        <?php echo strtoupper($data['location']['name']) ?>: <?php echo $data['category'] ? strtoupper($data['category']['name']) : 'ALL COACHES' ?>
      </h1>
      <h2>A view of all current <?php echo $data['location']['name'] ?><?php echo $data['category'] ? ' '.strtolower($data['category']['name']) : '' ?> coaches</h2>
      <p>Click on a coach to learn more, see when they are available, and book a coaching session.</p>
    </div>
  </div>
  <div class="et_pb_module et_pb_portfolio_0 et_pb_bg_layout_light et_pb_portfolio_grid clearfix">
    <div class="et_pb_ajax_pagination_container">
      <div class="et_pb_portfolio_grid_items">
        <?php
          $genericCoachImage = 'https://player2player.com/wp-content/uploads/2021/07/coach-icon-png-4.png';
          foreach($data['coaches'] as $id => $item) {
        ?>
        <div id="post-<?php echo $id?>" class="et_pb_portfolio_item et_pb_grid_item">
          <?php
            $fullName = "{$item->getFullName()}";
            $picture  =  $item->getPicture() ? $item->getPicture()->getFullPath() : $genericCoachImage;
            $profileUrl = $item->getSlug() ? "/coach/{$item->getSlug()->getValue()}" : "";
            $categories = [];
            foreach($item->getServiceList()->getItems() as $service) {
              $categoryId = $service->getCategoryId()->getValue();
              if (!array_key_exists($categoryId, $categories)) {
                $categories[$categoryId] = $service->getCategory();
              }
            }
            $countCategories = count($categories);
          ?>
          <a href="<?php echo $profileUrl ?>"
              title="<?php echo $fullName ?>">
            <span class="et_portfolio_image">
              <img loading="lazy"
                    src="<?php echo $picture ?>"
                    alt="<?php echo $fullName ?>"
                    style="width: 222px !important; height: 158px !important; object-fit: cover !important;"
              >
              <span class="et_overlay"/>
            </span>
          </a>
          <h2 class="et_pb_module_header">
            <a href="<?php echo $profileUrl ?>"
                title="<?php echo $fullName ?>"><?php echo $fullName ?></a>
          </h2>
          <?php if (!$data['category'] && $countCategories > 0) { ?>
            <p class="post-meta">
              <?php
                $i=1;
                foreach($categories as $category) {
                  $cat = ucfirst(strtolower($category->getName()->getValue()));
                  $catUrl = "/coaches/{$atts['location']}/{$category->getSlug()->getValue()}"
              ?>
              <a href="<?php echo $catUrl ?>"
                  title="<?php echo $cat ?>"><?php echo $cat ?></a><?php echo $i < $countCategories ? ',' : '' ?>
              <?php
                  $i += 1;
                }
              ?>
            </p>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
