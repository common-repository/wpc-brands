<?php
defined( 'ABSPATH' ) || exit;

class Wpcbr_Widget extends WP_Widget {
	public function __construct() {
		$widget_ops = [
			'classname'   => 'wpcbr_widget woocommerce widget_layered_nav',
			'description' => esc_html__( 'Show all product brands as a list.', 'wpc-brands' ),
		];

		$control_ops = [
			'width'  => 500,
			'height' => 350,
		];

		parent::__construct( 'wpcbr_widget', esc_html__( 'WPC Brands', 'wpc-brands' ), $widget_ops, $control_ops );
	}

	public function widget( $args, $instance ) {
		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}

		$show_image = ! empty( $instance['show_image'] ) ? intval( $instance['show_image'] ) : 0;
		$show_count = ! empty( $instance['show_count'] ) ? intval( $instance['show_count'] ) : 0;
		$hide_empty = ! empty( $instance['hide_empty'] ) ? intval( $instance['hide_empty'] ) : 0;

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$brands = get_terms( [
			'taxonomy'   => 'wpc-brand',
			'hide_empty' => $hide_empty,
		] );

		if ( ! empty( $brands ) ) {
			echo '<ul class="wpcbr_widget_brands">';

			foreach ( $brands as $brand ) {
				$logo  = get_term_meta( $brand->term_id, 'wpcbr_logo', true );
				$image = wp_get_attachment_image_url( $logo, 'thumbnail' );

				echo '<li class="wpcbr_widget_brand">';
				echo '<a href="' . esc_url( get_term_link( $brand ) ) . '">';

				if ( $show_image && $logo ) {
					echo '<span class="wpcbr_widget_brand_image"><img src="' . esc_url( $image ) . '" alt="' . esc_attr( $brand->name ) . '"/></span>';
				}

				echo '<span class="wpcbr_widget_brand_name">' . esc_html( $brand->name ) . '</span>';

				if ( $show_count ) {
					echo '<span class="wpcbr_widget_brand_count">' . esc_html( $brand->count ) . '</span>';
				}

				echo '</a>';
				echo '</li>';
			}

			echo '</ul>';
		}

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title      = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Brands', 'wpc-brands' );
		$show_image = ! empty( $instance['show_image'] ) ? intval( $instance['show_image'] ) : 0;
		$show_count = ! empty( $instance['show_count'] ) ? intval( $instance['show_count'] ) : 0;
		$hide_empty = ! empty( $instance['hide_empty'] ) ? intval( $instance['hide_empty'] ) : 0;
		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( esc_attr( 'Title:' ) ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
        </p><p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_image' ) ); ?>">
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_image' ) ); ?>" type="checkbox" value="1" <?php checked( 1, $show_image ); ?>> <?php esc_html_e( 'Show brand images', 'wpc-brands' ); ?>
            </label>
        </p><p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>">
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_count' ) ); ?>" type="checkbox" value="1" <?php checked( 1, $show_count ); ?>> <?php esc_html_e( 'Show product count', 'wpc-brands' ); ?>
            </label>
        </p><p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>">
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_empty' ) ); ?>" type="checkbox" value="1" <?php checked( 1, $hide_empty ); ?>> <?php esc_html_e( 'Hide empty brands', 'wpc-brands' ); ?>
            </label>
        </p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance               = [];
		$instance['title']      = ! empty( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['show_image'] = ! empty( $new_instance['show_image'] ) ? intval( $new_instance['show_image'] ) : 0;
		$instance['show_count'] = ! empty( $new_instance['show_count'] ) ? intval( $new_instance['show_count'] ) : 0;
		$instance['hide_empty'] = ! empty( $new_instance['hide_empty'] ) ? intval( $new_instance['hide_empty'] ) : 0;

		return $instance;
	}
}