<?php

function orbis_checklist_maybe_disable_emoji() {
	if ( filter_has_var( INPUT_POST, 'genereate' ) ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
	}

	// @see https://github.com/WordPress/WordPress/blob/4.5.2/wp-includes/default-filters.php#L146-L151
}

add_action( 'init', 'orbis_checklist_maybe_disable_emoji' );

/**
 * Checklist
 */
function orbis_checklist_shortcode( $atts ) {
	global $post;

	$atts = shortcode_atts( array(
		'number' => 50,
		'cols'   => 3,
	), $atts );

	$categories = get_terms( 'orbis_checklist_category', array(
		'hide_empty' => 0,
	) );

	if ( ! is_array( $categories ) ) {
		return;
	}

	foreach ( $categories as $category ) {
		$query = new WP_Query( array(
			'post_type'      => 'orbis_checklist_item',
			'posts_per_page' => -1,
			'no_found_rows'  => true,
			'tax_query' => array(
				array(
					'taxonomy' => 'orbis_checklist_category',
					'field'    => 'term_id',
					'terms'    => $category->term_id,
				),
			),
		) );

		$category->checklist_items = $query->posts;
	}

	ob_start();

	$data = filter_input( INPUT_POST, 'checklist', FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY );

	?>
	<form method="post" action="">
		<?php foreach ( $categories as $category ) : ?>

			<?php if ( ! empty( $category->checklist_items ) ) : ?>

				<h4><?php echo esc_html( $category->name ); ?></h4>

				<div class="panel-group" id="<?php echo esc_attr( $category->slug ); ?>" role="tablist" aria-multiselectable="true">

					<?php foreach ( $category->checklist_items as $post ) : ?>

						<div class="panel panel-default">
							<?php

							setup_postdata( $post );

							$name = sprintf(
								'checklist[%s]',
								get_the_ID()
							);

							$item = array(
								'description' => '',
								'checked'     => false,
							);

							if ( isset( $data[ get_the_ID() ] ) ) {
								$item = $data[ get_the_ID() ];
							}

							?>
							<div class="panel-heading" role="tab" id="heading-<?php the_ID(); ?>">
								<input type="checkbox" name="<?php echo esc_attr( $name . '[checked]' ); ?>" value="1" <?php checked( $item['checked'] ); ?> />

								<a class="collapsed" class="collapse" role="button" data-toggle="collapse" data-parent="#<?php echo esc_attr( $category->slug ); ?>" href="#collapse-<?php the_ID(); ?>" aria-expanded="true" aria-controls="collapse-<?php the_ID(); ?>">
									<?php the_title(); ?>
								</a>
							</div>

							<div id="collapse-<?php the_ID(); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php the_ID(); ?>">
								<div class="panel-body">
									<?php the_content(); ?>

									<p>
										<small><a href="<?php the_permalink(); ?>"><?php the_permalink(); ?></a></small>
									</p>

									<p>
										<textarea name="<?php echo esc_attr( $name . '[description]' ); ?>"><?php echo esc_textarea( $item['description'] ); ?></textarea>
									</p>
								</div>
							</div>
						</div>

					<?php endforeach; ?>
				</div>

			<?php
				wp_reset_postdata();
				endif;
			?>

		<?php endforeach; ?>

		<input class="btn btn-primary" type="submit" name="genereate" value="Generate" />
	</form>

	<?php

	if ( filter_has_var( INPUT_POST, 'genereate' ) ) {
		echo '<h4>Overview</h4>';

		echo '<ul>';
		foreach ( $categories as $category ) {
			echo '<li>';
			echo esc_html( $category->name );
			echo '<ul>';
			foreach ( $category->checklist_items as $post ) {
				setup_postdata( $post );

				$item = array(
					'description' => '',
					'checked'     => false,
				);

				if ( isset( $data[ get_the_ID() ] ) ) {
					$item = $data[ get_the_ID() ];
				}

				echo '<li>';
				echo $item['checked'] ? '✓' : '✗';
				echo ' ';
				the_title();

				if ( $item['description'] ) {
					$description = $item['description'];
					$description = wptexturize( $description );
					$description = convert_chars( $description );
					$description = make_clickable( $description );
					$description = force_balance_tags( $description );
					$description = convert_smilies( $description );

					echo '<ul>';
					echo '<li>', wp_kses_post( $description ), '</li>';
					echo '</ul>';
				}

				echo '</li>';
			}
			echo '</ul>';
			echo '</li>';
		}
		echo '</ul>';
	}

	$output = ob_get_contents();

	ob_end_clean();

	return $output;
}

add_shortcode( 'checklist', 'orbis_checklist_shortcode' );
