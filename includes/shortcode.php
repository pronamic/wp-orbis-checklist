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

	foreach ( $categories as $category ) : ?>

		<?php if ( ! empty( $category->checklist_items ) ) : ?>

			<h4><?php echo esc_html( $category->name ); ?></h4>

			<div class="accordion mb-4" id="<?php echo esc_attr( $category->slug ); ?>">

				<?php foreach ( $category->checklist_items as $post ) : ?>

					<div class="accordion-item">
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

						$id = 'collapse-' . get_the_ID();

						?>
						<h5 class="accordion-header">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo \esc_attr( $id ); ?>" aria-expanded="false" aria-controls="<?php echo \esc_attr( $id ); ?>">
								<?php the_title(); ?>
							</button>
						</h5>

						<div id="<?php echo \esc_attr( $id ); ?>" class="accordion-collapse collapse" data-bs-parent="#<?php echo esc_attr( $category->slug ); ?>">
							 <div class="accordion-body">
								<?php the_content(); ?>

								<p>
									<small><a href="<?php the_permalink(); ?>"><?php the_permalink(); ?></a></small>
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
