<?php declare( strict_types = 1 );

namespace Trasweb\Plugins\DisplayMetadata\Metabox;

use ArrayIterator;
use Trasweb\Plugins\DisplayMetadata\Plugin;

class Vars_Iterator extends ArrayIterator {

	protected const META_LIST_VIEW = Plugin::VIEWS_PATH . '/meta_list.php';

	private $depth;

	/**
	 * Named constructor.
	 *
	 * @param array $vars_list List of vars.
	 * @param int   $depth     Current depth for these vars.
	 *
	 * @return static
	 */
	public static function from_vars_list( array $vars_list, int $depth = 1 ): self {
		$iterator        = new self( $vars_list, ArrayIterator::STD_PROP_LIST );
		$iterator->depth = $depth;

		return $iterator;
	}

	/**
	 * Retrieve current value of current meta.
	 *
	 * @return $this|mixed|string
	 */
	public function current() {
		$meta_value = parent::current();
		$meta_value = maybe_unserialize( $meta_value );

		if ( is_array( $meta_value ) ) {
			ksort( $meta_value );

			return Vars_Iterator::from_vars_list( $meta_value, $this->depth + 1 );
		}

		if ( is_null( $meta_value ) ) {
			return 'NULL';
		}

		return htmlentities( (string) $meta_value );
	}

	/**
	 * Current depth.
	 *
	 * @return mixed
	 */
	private function get_depth(): int {
		return (int) $this->depth;
	}

	/**
	 * Genererate a view for current metadata list.
	 *
	 * @return string
	 */
	public function __toString() {
		$metadata_list = $this;

		ob_start();
		( static function () use ( $metadata_list ) {
			include static::META_LIST_VIEW;
		} )();

		return \ob_get_clean() ?: '';
	}
}