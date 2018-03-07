<?php
namespace jri\models;

/**
 * Class ImageSize
 *
 * @package jri\objects
 */
class ImageSize {
	/**
	 * Image size unique key
	 *
	 * @var string
	 */
	public $key;

	/**
	 * Image width
	 *
	 * @var int
	 */
	public $w;

	/**
	 * Image height
	 *
	 * @var int
	 */
	public $h;

	/**
	 * Image crop options
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_image_size/
	 *
	 * @var bool|array
	 */
	public $crop = false;

	/**
	 * ImageSize constructor.
	 *
	 * @param string       $key Image size unique key.
	 * @param array|string $params Size width, height, crop  options.
	 * @param array        $retina_options Retina options.
	 *
	 * @throws \Exception  Wrong size parameter passed.
	 */
	public function __construct( $key, $params, $retina_options ) {
		if ( ( is_array( $params ) && count( $params ) < 2 )
			|| ( is_string( $params ) && strpos( $params, 'x' ) === false )
		) {
			throw new \Exception( "ImageSize::_construct() : Wrong size parameters passed for key '{$key}'" );
		}

		if ( ! is_array( $params ) ) {
			$params = explode( 'x', $params );
		}
		if ( 3 > count( $params ) ) {
			$params[] = false;
		}
		$params = array_values( $params );
		$this->key  = $key;
		$this->w    = absint( $params[0] );
		$this->h    = absint( $params[1] );
		if( is_array( $params[2] ) ) {
			$this->crop = $params[2];
		} else {
			$this->crop = absint( $params[2] );
		}
		$this->register();
	}

	/**
	 * Call wordpress function to register current valid size.
	 */
	public function register() {
		if ( in_array( $this->key, array( 'thumbnail', 'medium', 'large', 'medium_large' ) ) ) {
			update_site_option( "{$this->key}_size_w", $this->w );
			update_site_option( "{$this->key}_size_h", $this->h );
			update_site_option( "{$this->key}_crop", ! empty( $this->crop ) );
		}
	}

	/**
	 * Prepare unique image size for retina size.
	 *
	 * @param string $key Image size name.
	 * @param string $retina_descriptor Retina descriptor (like 2x, 3x).
	 *
	 * @return string
	 */
	public static function get_retina_key( $key, $retina_descriptor ) {
		return "{$key} @{$retina_descriptor}";
	}
}
