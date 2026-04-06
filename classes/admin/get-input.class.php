<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * User Input Components.
 *
 * Generate all user input components for forms.
 *
 * @package error-monitor
 * @author Jefferson Real <jeff@webguyjeff.com>
 * @copyright Copyright (c) 2024, Jefferson Real
 * @license GPL3+
 * @link https://webguyjeff.com
 */
class Get_Input {


	/**
	 * Output an input control.
	 */
	public static function input( $props ) {
		$setting     = $props['setting'];
		$wp_option   = $props['wp_option'];
		$value       = $props['value'];
		$label       = $props['label'];
		$description = $props['description'] ?? '';
		$type        = $props['type'];
		$atttributes = $props['atttributes'] ?? array();
		$flags       = $props['flags'] ?? array();
		$classes     = $props['classes'] ? ' ' . $props['classes'] : '';

		$id    = $wp_option . "_$setting";
		$attrs = '';
		foreach ( array_merge( $atttributes, $flags ) as $key => $val ) {
			$attrs .= is_numeric( $key ) ? ( $val . "\n" ) : ( $key . '="' . $val . '"' . "\n" );
		}
		$allowed_html = array(
			'input' => array(
				'step' => true,
				'min'  => true,
				'max'  => true,
			),
		);
		?>
		<div class="field field-singleline<?php echo esc_attr( $classes ); ?>">
			<label
				for="<?php echo esc_attr( $id . '_input' ); ?>"
				id="<?php echo esc_attr( $id . '_label' ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
			<input
				data-em-key="<?php echo esc_attr( $setting ); ?>"
				type="<?php echo esc_attr( $type ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				id="<?php echo esc_attr( $id . '_input' ); ?>"
				aria-labelledby="<?php echo esc_attr( $id . '_label' ); ?>"
				aria-describedby="<?php echo esc_attr( $id . '_description' ); ?>"
				<?php echo wp_kses( $attrs, $allowed_html ); ?>>
			<?php if ( $description ) : ?>
				<p
					class="field__description"
					id="<?php echo esc_attr( $id . '_description' ); ?>">
					<?php echo esc_html( $description ); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}


	/**
	 * Output a toggle control.
	 */
	public static function toggle( $props ) {
		$setting     = $props['setting'];
		$wp_option   = $props['wp_option'];
		$value       = $props['value'];
		$label       = $props['label'];
		$description = $props['description'] ?? '';

		$id      = $wp_option . "_$setting";
		$checked = checked( '1', $value, false );
		?>
		<div class="field field-toggle">
			<input
				data-em-key="<?php echo esc_attr( $setting ); ?>"
				type="checkbox"
				value="1"
				id="<?php echo esc_attr( $id . '_input' ); ?>"
				aria-labelledby="<?php echo esc_attr( $id . '_label' ); ?>"
				aria-describedby="<?php echo esc_attr( $id . '_description' ); ?>"
				<?php echo esc_attr( $checked ); ?>>
			<label
				for="<?php echo esc_attr( $id . '_input' ); ?>"
				id="<?php echo esc_attr( $id . '_label' ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
			<?php if ( $description ) : ?>
				<p
					class="field__description"
					id="<?php echo esc_attr( $id . '_description' ); ?>">
					<?php echo esc_html( $description ); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}


	/**
	 * Output a select control.
	 */
	public static function select( $props ) {
		$setting     = $props['setting'];
		$wp_option   = $props['wp_option'];
		$value       = $props['value'];
		$options     = $props['options'];
		$label       = $props['label'];
		$description = $props['description'] ?? '';
		$classes     = isset( $props['classes'] ) && $props['classes'] ? ' ' . $props['classes'] : '';

		$id           = $wp_option . "_$setting";
		$allowed_html = array(
			'option' => array(
				'value'    => true,
				'selected' => true,
			),
		);
		$options_html = '';
		foreach ( $options as $opt_val => $opt_label ) {
			$options_html .= sprintf(
				'<option value="%s" %s>%s</option>%s',
				esc_attr( $opt_val ),
				selected( $value, $opt_val, false ),
				esc_html( $opt_label ),
				"\n"
			);
		}
		?>
		<div class="field field-singleline field-select<?php echo esc_attr( $classes ); ?>">
			<label
				for="<?php echo esc_attr( $id . '_input' ); ?>"
				id="<?php echo esc_attr( $id . '_label' ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
			<select
				data-em-key="<?php echo esc_attr( $setting ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				id="<?php echo esc_attr( $id . '_input' ); ?>"
				aria-labelledby="<?php echo esc_attr( $id . '_label' ); ?>"
				aria-describedby="<?php echo esc_attr( $id . '_description' ); ?>">
				<?php echo wp_kses( $options_html, $allowed_html ); ?>
			</select>
			<?php if ( $description ) : ?>
				<p
					class="field__description"
					id="<?php echo esc_attr( $id . '_description' ); ?>">
					<?php echo esc_html( $description ); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}


	/**
	 * Output an action button.
	 */
	public static function action_buttons( $buttons ) {
		echo '<div class="adminButtonRow">';
		foreach ( $buttons as $props ) {
			$action               = $props['action'];
			$label                = $props['label'];
			$primary_or_secondary = $props['primary_or_secondary'] ?? 'secondary';
			$atttributes          = $props['atttributes'] ?? array();
			$flags                = $props['flags'] ?? array();

			$attrs = '';
			foreach ( array_merge( $atttributes, $flags ) as $key => $val ) {
				$attrs .= is_numeric( $key ) ? ( $val . "\n" ) : ( $key . '="' . $val . '"' . "\n" );
			}
			$allowed_html = array(
				'button' => array(
					'data-*' => true,
				),
			);
			?>
				<button
					data-em-action="<?php echo esc_attr( $action ); ?>"
					type="button"
					class="button button-<?php echo esc_attr( $primary_or_secondary ); ?>"
					<?php echo wp_kses( $attrs, $allowed_html ); ?>>
					<?php echo esc_html( $label ); ?>
				</button>
			<?php
		}
		echo '</div>';
	}
}
