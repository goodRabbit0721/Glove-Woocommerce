<?php

namespace WPEM;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Fields {

	/**
	 * An array of fields
	 *
	 * @var array
	 */
	private $fields = [];

	/**
	 * Class constructor
	 *
	 * @param array $fields
	 */
	public function __construct( $fields ) {

		if ( empty( $fields ) || ! is_array( $fields ) ) {

			return;

		}

		$step = wpem_get_current_step()->name;

		/**
		 * Filter the fields for the current step
		 *
		 * @since 2.0.0
		 *
		 * @var array
		 */
		$this->fields = (array) apply_filters( "wpem_step_{$step}_fields", $fields );

	}

	/**
	 * Return an array of fields with errors
	 *
	 * @return array|bool
	 */
	private function get_error_fields() {

		if ( ! filter_input( INPUT_GET, 'error' ) ) {

			return false;

		}

		$fields = filter_input( INPUT_GET, 'fields' );

		if ( ! $fields ) {

			return false;

		}

		return explode( ',', $fields );

	}

	/**
	 * Display error notice when missing required fields
	 *
	 * @action wpem_template_notices
	 */
	public function error_notice() {

		$errors = $this->get_error_fields();

		if ( ! $errors ) {

			return;

		}

		echo '<ul class="wpem-notice-list">';

		foreach ( $errors as $error ) {

			$field = $this->get_by( 'name', $error );

			if ( ! $field ) {

				continue;

			}

			$label = ! empty( $field['label'] ) ? $field['label'] : $error;

			$message = __( sprintf( '<strong>%s</strong> is a required field.', esc_html( $label ) ), 'wp-easy-mode' );

			printf(
				'<li class="wpem-notice-list-item error-notice">%s</li>',
				$message // xss ok
			);

		}

		echo '</ul>';

	}

	/**
	 * Return a field by key/value pair match
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 *
	 * @return array|bool
	 */
	public function get_by( $key, $value ) {

		foreach ( $this->fields as $field ) {

			if ( isset( $field[ $key ] ) && $value === $field[ $key ] ) {

				return $field;

			}

		}

		return false;

	}

	/**
	 * Display fields
	 */
	public function display() {

		$errors = $this->get_error_fields();

		foreach ( $this->fields as $field ) {

			if ( empty( $field['name'] ) || empty( $field['type'] ) ) {

				continue;

			}

			$classes = [ 'wpem-step-field' ];

			if ( is_array( $errors ) && in_array( $field['name'], $errors ) ) {

				$classes[] = 'wpem-step-field-error';

				$field['value'] = '';

			}

			printf( '<p class="%s">', implode( ' ', $classes ) );

			$method = $field['type'] . '_field';

			if ( is_callable( [ $this, $method ] ) ) {

				$this->{$method}( $field );

			} else {

				$this->text_field( $field );

			}

			if ( ! empty( $field['description'] ) ) {

				echo '<br>';

				printf( '<span class="description">%s</span>', esc_html( $field['description'] ) );

			}

			echo '</p>';

		}

	}

	/**
	 * Checkbox field display
	 *
	 * @param array $field
	 */
	public function checkbox_field( $field ) {

		$defaults = [
			'name'     => null,
			'id'       => null,
			'label'    => null,
			'value'    => 1,
			'required' => false,
			'atts'     => [],
			'choices'  => [],
		];

		extract( $defaults );

		extract( $field, EXTR_IF_EXISTS );

		if ( empty( $name ) || empty( $choices ) ) {

			return;

		}

		$atts['required'] = ! empty( $required );

		if ( ! empty( $label ) ) {

			printf(
				'<label>%s</label>',
				esc_html( $label )
			);

			echo '<br>';

		}

		foreach ( (array) $choices as $key => $label ) {

			$name = ( count( $choices ) > 1 ) ? sprintf( '%s[%s]', $name, sanitize_key( $key ) ) : $name;

			printf(
				'<label><input type="checkbox" name="%s" value="%s" %s %s> %s</label>',
				esc_attr( $name ),
				esc_attr( $value ),
				$this->parse_atts( $atts ),
				checked( in_array( $key, (array) $value ), true, false ),
				esc_html( $label )
			);

			if ( false !== next( $choices ) ) {

				echo '<br>';

			}

		}

	}

	/**
	 * Radio field display
	 *
	 * @param array $field
	 */
	public function radio_field( $field ) {

		$defaults = [
			'name'     => null,
			'id'       => null,
			'label'    => null,
			'value'    => null,
			'required' => false,
			'atts'     => [],
			'choices'  => [],
		];

		extract( $defaults );

		extract( $field, EXTR_IF_EXISTS );

		if ( empty( $name ) || empty( $choices ) ) {

			return;

		}

		$atts['required'] = ! empty( $required );

		if ( ! empty( $label ) ) {

			printf(
				'<label>%s</label>',
				esc_html( $label )
			);

			echo '<br>';

		}

		echo '<span class="wpem-inline-radio">';

		foreach ( (array) $choices as $key => $label ) {

			printf(
				'<label><input type="radio" name="%s" value="%s" %s %s> %s</label>',
				esc_attr( $name ),
				esc_attr( $key ),
				$this->parse_atts( $atts ),
				checked( $key, $value, false ),
				esc_html( $label )
			);

		}

		echo '</span>';

	}

	/**
	 * Select field display
	 *
	 * @param array $field
	 */
	public function select_field( $field ) {

		$defaults = [
			'name'     => null,
			'id'       => null,
			'label'    => null,
			'value'    => '',
			'required' => false,
			'atts'     => [],
			'choices'  => [],
		];

		extract( $defaults );

		extract( $field, EXTR_IF_EXISTS );

		if ( empty( $name ) || empty( $choices ) ) {

			return;

		}

		$id = ! empty( $id ) ? $id : $name;

		$atts['required'] = ! empty( $required );

		if ( ! empty( $label ) ) {

			printf(
				'<label for="%s">%s</label>',
				esc_attr( $id ),
				esc_html( $label )
			);

			echo '<br>';

		}

		printf(
			'<select name="%s" id="%s" %s>',
			esc_attr( $name ),
			esc_attr( $id ),
			$this->parse_atts( $atts )
		);

		foreach ( (array) $choices as $key => $label ) {

			printf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $key ),
				selected( $key, $value, false ),
				esc_html( $label )
			);

		}

		echo '</select>';

	}

	/**
	 * Select field display using Select2
	 *
	 * @param array $field
	 */
	public function jq_select_field( $field ) {

		$atts = isset( $field['atts'] ) ? $field['atts'] : [];

		$atts['class'] = ( isset( $atts['class'] ) ? $atts['class'] : '' ) . ' jq_select';

		$field['atts'] = $atts;

		$this->select_field( $field );

	}

	/**
	 * Textarea field display
	 *
	 * @param array $field
	 */
	public function textarea_field( $field ) {

		$defaults = [
			'name'     => null,
			'id'       => null,
			'label'    => null,
			'value'    => '',
			'required' => false,
			'atts'     => [],
		];

		extract( $defaults );

		extract( $field, EXTR_IF_EXISTS );

		if ( empty( $name ) ) {

			return;

		}

		$id = ! empty( $id ) ? $id : $name;

		$atts['required'] = ! empty( $required );

		if ( ! empty( $label ) ) {

			printf(
				'<label for="%s">%s</label>',
				esc_attr( $id ),
				esc_html( $label )
			);

			echo '<br>';

		}

		printf(
			'<textarea name="%s" id="%s" %s>%s</textarea>',
			esc_attr( $name ),
			esc_attr( $id ),
			$this->parse_atts( $atts ),
			wp_kses_post( $value )
		);

	}

	/**
	 * Text field display (default)
	 *
	 * @param array $field
	 */
	public function text_field( $field ) {

		$defaults = [
			'name'     => null,
			'id'       => null,
			'label'    => null,
			'type'     => 'text',
			'value'    => '',
			'required' => false,
			'atts'     => [],
		];

		extract( $defaults );

		extract( $field, EXTR_IF_EXISTS );

		if ( empty( $name ) || empty( $type ) ) {

			return;

		}

		$id = ! empty( $id ) ? $id : $name;

		$atts['required'] = ! empty( $required );

		if ( ! empty( $label ) ) {

			printf(
				'<label for="%s">%s</label>',
				esc_attr( $id ),
				esc_html( $label )
			);

			echo '<br>';

		}

		printf(
			'<input type="%s" name="%s" id="%s" value="%s" %s>',
			esc_attr( $type ),
			esc_attr( $name ),
			esc_attr( $id ),
			esc_attr( $value ),
			$this->parse_atts( $atts )
		);

	}

	/**
	 * Display parsed field element attributes
	 *
	 * @param  array $atts
	 *
	 * @return string
	 */
	private function parse_atts( $atts ) {

		if ( empty( $atts ) || ! is_array( $atts ) ) {

			return;

		}

		$output = [];

		foreach ( $atts as $key => $value ) {

			if ( is_array( $value ) || is_null( $value ) || false === $value ) {

				continue;

			}

			if ( true === $value ) {

				$output[] = sanitize_key( $key );

				continue;

			}

			$output[] = sprintf( '%s="%s"', sanitize_key( $key ), esc_attr( $value ) );

		}

		return implode( ' ', $output );

	}

	/**
	 * Save fields
	 *
	 * @return array|bool
	 */
	public function save() {

		$saved   = [];
		$invalid = [];

		foreach ( $this->fields as $field ) {

			preg_match( '/\[(.*?)\]/', $field['name'], $key ); // Get key from brackets

			$key       = isset( $key[1] ) ? $key[1] : null;
			$name      = preg_replace( '/[\[].*[\]]/', '', $field['name'] ); // Strip brackets
			$value     = ( $key && isset( $_POST[ $name ][ $key ] ) ) ? $_POST[ $name ][ $key ] : ( isset( $_POST[ $name ] ) ? $_POST[ $name ] : null ); // Support arrays
			$sanitizer = empty( $field['sanitizer'] ) ? null : $field['sanitizer'];

			// Maybe sanitize
			if ( $value && $sanitizer && is_callable( $sanitizer ) ) {

				$value = is_array( $value ) ? array_map( $sanitizer, $value ) : $sanitizer( $value );

			}

			// Maybe validate
			if ( ! $value && ! empty( $field['required'] ) ) {

				$invalid[] = ( $key ) ? sprintf( '%s[%s]', $name, $key ) : $name;

				continue;

			}

			// Maybe use default
			if ( ! $value && isset( $field['default'] ) ) {

				$value = $field['default'];

			}

			$result = false;

			// Only save WPEM options directly to the database
			if ( 0 === strpos( $name, 'wpem_' ) && empty( $field['skip_option'] ) ) {

				if ( $key ) {

					$option         = (array) get_option( $name, [] );
					$option[ $key ] = $value;
					$value          = $option;

				}

				$result = update_option( $name, $value );

			}

			// Maybe save to log
			if ( empty( $field['skip_log'] ) ) {

				$log = new Log;

				$result = $log->add_step_field( $name, $value );

			}

			if ( $result ) {

				$saved[ $name ] = $value;

			}

		}

		if ( ! empty( $invalid ) ) {

			wp_safe_redirect(
				add_query_arg(
					[
						'step'   => wpem_get_current_step()->name,
						'error'  => true,
						'fields' => implode( ',', $invalid ),
					],
					wpem_get_wizard_url()
				)
			);

			exit;

		}

		return $saved;

	}

}
