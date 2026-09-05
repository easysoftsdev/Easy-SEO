<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * The class creates object for plugin classes
 */
class ESQ_Classes_ObjController {

	/**
	 *
	 *
	 * @var array of instances
	 */
	public static $instances;

	/**
	 * @param  $className $className
	 * @param array $args
	 *
	 * @return object $className|false
	 */
	public static function getClass( $className, $args = array() ) {

		if ( $class = self::getClassPath( $className ) ) {
			if ( ! isset( self::$instances[ $className ] ) ) {
				/* check if class is already defined */
				if ( ! class_exists( $className ) ) {
					try {
						self::includeClass( $class['dir'], $class['name'] );

						//check if this is an abstract class
						$check    = new ReflectionClass( $className );
						$abstract = $check->isAbstract();
						if ( ! $abstract ) {
							self::$instances[ $className ] = new $className();
							if ( ! empty( $args ) ) {
								call_user_func_array( array( self::$instances[ $className ], '__construct' ), $args );
							}

							return self::$instances[ $className ];
						} else {
							self::$instances[ $className ] = true;
						}
					} catch ( Exception $e ) {
					}
				}
			} else {
				return self::$instances[ $className ];
			}
		}

		return false;
	}

	/**
	 * Get a new instance of the class
	 *
	 * @param  $className
	 * @param array $args
	 *
	 * @return bool|mixed
	 */
	public static function getNewClass( $className, $args = array() ) {
		$instance = false;
		if ( $class = self::getClassPath( $className ) ) {
			/* check if class is already defined */
			try {
				if ( ! class_exists( $className ) ) {
					self::includeClass( $class['dir'], $class['name'] );
				}

				//check if this is an abstract class
				$check    = new ReflectionClass( $className );
				$abstract = $check->isAbstract();
				if ( ! $abstract ) {
					$instance = new $className();

					if ( ! empty( $args ) ) {
						call_user_func_array( array( $instance, '__construct' ), $args );
					}

					return $instance;
				} else {
					$instance = true;
				}
			} catch ( Exception $e ) {

			}

		}

		return $instance;
	}

	/**
	 * @param  $classDir
	 * @param  $className
	 *
	 * @throws Exception
	 */
	private static function includeClass( $classDir, $className ) {
		$file = $classDir . $className . '.php';
		try {
			if ( file_exists( $file ) ) {
				include_once $file;
			}
		} catch ( Exception $e ) {
			throw new Exception( 'Controller Error: ' . $e->getMessage() );
		}
	}

	/**
	 * @param  $className
	 * @param array $args
	 *
	 * @return stdClass
	 */
	public static function getDomain( $className, $args = array() ) {
		try {
			if ( $class = self::getClassPath( $className ) ) {
				self::includeClass( $class['dir'], $class['name'] );

				return new $className( $args );
			}
		} catch ( Exception $e ) {

		}

		return new stdClass();
	}


	/**
	 * Check if the class is correctly set
	 *
	 * @param string $className
	 *
	 * @return boolean
	 */
	private static function checkClassPath( $className ) {
		$path = preg_split( '/[_]+/', $className );
		if ( is_array( $path ) && count( $path ) > 1 ) {
			if ( in_array( _ESQ_NAMESPACE_, $path ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the path of the class and name of the class
	 *
	 * @param string $className
	 *
	 * @return array | boolean
	 * array(
	 * dir - absolute path of the class
	 * name - the name of the file
	 * }
	 */
	public static function getClassPath( $className ) {
		$dir = '';


		if ( self::checkClassPath( $className ) ) {
			$path = preg_split( '/[_]+/', $className );
			for ( $i = 1; $i < sizeof( $path ) - 1; $i ++ ) {
				$dir .= strtolower( $path[ $i ] ) . '/';
			}

			$class = array(
				'dir'  => _ESQ_ROOT_DIR_ . $dir,
				'name' => $path[ sizeof( $path ) - 1 ]
			);

			if ( file_exists( $class['dir'] . $class['name'] . '.php' ) ) {
				return $class;
			}
		}

		return false;
	}

}