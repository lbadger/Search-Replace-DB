<?php
date_default_timezone_set( 'America/Denver' );

// include the srdb class
require_once( 'srdb.class.php' );

class xennsoft_srdb extends icit_srdb {

	private $args;

	public function __construct($args) {
		$this->args = $args;
		return parent::__construct($this->args);
	}

	public function log( $type ) {

		$args = array_slice( func_get_args(), 1 );

		$output = "";

		switch( $type ) {
			case 'error':
				list( $error_type, $error ) = $args;
				$output .= "$error_type: $error";
				break;
			case 'search_replace_table_start':
				list( $table, $search, $replace ) = $args;
				$output .= "{$table}: replacing {$search} with {$replace}";
				break;
			case 'search_replace_table_end':
				list( $table, $report ) = $args;
				$time = number_format( $report[ 'end' ] - $report[ 'start' ], 8 );
				$output .= "{$table}: {$report['rows']} rows, {$report['change']} changes found, {$report['updates']} updates made in {$time} seconds";
				break;
			case 'search_replace_end':
				list( $search, $replace, $report ) = $args;
				$time = number_format( $report[ 'end' ] - $report[ 'start' ], 8 );
				$dry_run_string = $this->dry_run ? "would have been" : "were";
				$output .= "
Replacing {$search} with {$replace} on {$report['tables']} tables with {$report['rows']} rows
{$report['change']} changes {$dry_run_string} made
{$report['updates']} updates were actually made
It took {$time} seconds";
				break;
			case 'update_engine':
				list( $table, $report, $engine ) = $args;
				$output .= $table . ( $report[ 'converted' ][ $table ] ? ' has been' : 'has not been' ) . ' converted to ' . $engine;
				break;
			case 'update_collation':
				list( $table, $report, $collation ) = $args;
				$output .= $table . ( $report[ 'converted' ][ $table ] ? ' has been' : 'has not been' ) . ' converted to ' . $collation;
				break;
		}

		if ( $this->verbose )
			echo $output . "\n";

	}

    public function get_tables() {
        $all_tables = parent::get_tables();

		if (isset($this->args['exclude_tables'])) {
            $exclude = (is_array($this->args['exclude_tables']) ? $this->args['exclude_tables'] : explode( ',', $this->args['exclude_tables']));

			foreach ( $exclude as $value ) {
				if (strpos( $value, '*' ) !== false ) {
					$tablePrefix = substr($value, 0, -1);
					foreach ($all_tables as $tableKey => $tableValue)
						if (strpos( $tableKey, $tablePrefix) !== false)
							unset($all_tables[$tableKey]);
				}
			}
			
			foreach ( $exclude as $value ) {
				if ( array_key_exists( $value, $all_tables ) ) {
					unset( $all_tables[ $value ] );
				}
			}
		}

		return $all_tables;
	}



}
