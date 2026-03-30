<?php
namespace WebGuyJeff\Error_Monitor;

/**
 * Log View Model.
 *
 * Prepares structured log data for rendering.
 */
class Log_View_Model {

	/**
	 * Build grouped logs for rendering.
	 */
	public static function grouped( array $entries ): array {

		$grouped = Log_Formatter::grouped( $entries );

		$output = array();

		foreach ( $grouped as $timestamp => $logs ) {

			$output[] = array(
				'timestamp' => $timestamp,
				'label'     => gmdate( 'd-M-Y H:i:s \U\T\C', $timestamp ),
				'logs'      => $logs,
			);
		}

		return $output;
	}
}
