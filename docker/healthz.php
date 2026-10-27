<?php
/**
 * Readiness check for Docker and Kubernetes: 200 when the database answers,
 * 503 otherwise. Loads no WordPress code, so it is cheap to call often.
 */

header( 'Content-Type: text/plain' );
header( 'Cache-Control: no-store' );

$password = getenv( 'WORDPRESS_DB_PASSWORD' );
$file     = getenv( 'WORDPRESS_DB_PASSWORD_FILE' );
if ( ( $password === false || $password === '' ) && $file && is_readable( $file ) ) {
    $password = rtrim( file_get_contents( $file ), "\r\n" );
}

mysqli_report( MYSQLI_REPORT_OFF );
$db = @mysqli_connect(
    getenv( 'WORDPRESS_DB_HOST' ) ?: 'db',
    getenv( 'WORDPRESS_DB_USER' ) ?: 'wordpress',
    (string) $password,
    getenv( 'WORDPRESS_DB_NAME' ) ?: 'wordpress'
);

if ( ! $db ) {
    http_response_code( 503 );
    exit( "database unavailable\n" );
}
mysqli_close( $db );
echo "ok\n";
