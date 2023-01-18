<div class="et_pb_row et_pb_row_0">
	<div class="et_pb_column et_pb_column_4_4 et_pb_column_0  et_pb_css_mix_blend_mode_passthrough et-last-child">
		<div class="et_pb_module et_pb_text et_pb_text_0  et_pb_text_align_left et_pb_bg_layout_light">
			<div class="et_pb_text_inner">
				<h1 style="text-align: center;"><?php echo $data['fullName'] ?></h1>
				<h2 style="text-align: center;">
          <a href="/coaches/<?php echo $data['location']['slug'] ?>" >
            <?php echo $data['location']['name'] ?>
          </a>          
        </h2>
			</div>
		</div>
		<!-- .et_pb_text -->
	</div>
	<!-- .et_pb_column -->
</div>
<!-- .et_pb_row -->
<div class="et_pb_row_1">
	<div class="et_pb_column et_pb_column_1_3 et_pb_column_1  et_pb_css_mix_blend_mode_passthrough">
		<div class="et_pb_module et_pb_image et_pb_image_0">
			<span class="et_pb_image_wrap ">
				<img loading="lazy"
					 src="<?php echo $data['picture'] ?>"
					 alt="<?php echo $data['fullName'] ?>"
					 title="<?php echo $data['fullName'] ?>"
           style="width: 200px !important; height: 200px !important; object-fit: cover !important; border-radius: 50%;"
					 />
		</div>
		<div class="et_pb_module et_pb_text et_pb_text_1  et_pb_text_align_left et_pb_bg_layout_light">
			<div class="et_pb_text_inner">
        <?php 
          $countCategories = count($data['categories']);
          $locationSlug = $data['location']['slug'];          
        ?>
				<h3>Coaching categories</h3>
        <p>
          <?php 
            if ($countCategories > 0) {
              $i=1;
              foreach($data['categories'] as $category) {    
                $catInfo = $category['category'];
                $cat = ucfirst(strtolower($catInfo['categoryName']));
                $catUrl = "/coaches/$locationSlug/{$catInfo['categorySlug']}";
          ?>
          <a style="color: #666;" href="<?php echo $catUrl ?>"
             title="<?php echo $cat ?>"><?php echo $cat ?></a><?php echo $i < $countCategories ? ', ' : '' ?>
          <?php
                $i+= 1;
            }
          }
          ?>
        </p>
				<h3>Services</h3>
        <?php 
          if ($countCategories > 0) {
            $i = 1;
            foreach($data['categories'] as $category) {    
              $catInfo = $category['category'];
              $services = $category['services'];
              $cat = ucfirst(strtolower($catInfo['categoryName']));
              $catUrl = "/coaches/$locationSlug/{$catInfo['categorySlug']}";
        ?>
        <p>
          <a href="<?php echo $catUrl ?>"
             title="<?php echo $cat ?>"><?php echo $cat ?>:
          </a>
          <br/>
        <?php
              $i+= 1;
              $j = 1;
              $countServices = count($services);
              foreach($services as $service) {
                echo $service, $j < $countServices ? ', ' : '';
                $j+= 1;
              } // end for
        ?>
        </p>
        <?php      
            } // end for  
          } // end if        
        ?>
				<h3>Key accomplishments</h3>
				<p>
          <?php echo $data['notes'] ?>
				</p>
			</div>
		</div>
		<!-- .et_pb_text -->
	</div>
	<!-- .et_pb_column -->
	<div class="et_pb_column et_pb_column_2_3 et_pb_column_2  et_pb_css_mix_blend_mode_passthrough et-last-child">
		<div class="et_pb_module et_pb_text et_pb_text_2  et_pb_text_align_left et_pb_bg_layout_light">
			<div class="et_pb_text_inner">
				<h1 style="text-align: center;">Book A Lesson</h1>
				<?php echo do_shortcode( "[ameliastepbooking employee='{$data['id']}']" ) ?>
			</div>
		</div>
		<!-- .et_pb_text -->
	</div>
	<!-- .et_pb_column -->
</div>
<!-- .et_pb_row -->
