<?php
namespace OWC\Store;

if ( ! defined( 'ABSPATH' ) ) exit;

use Monolog\Logger,
	Monolog\Handler\StreamHandler;

class Store {

	/*
	|-----------------------------------------------------------
	| PROPERTIES
	|-----------------------------------------------------------
	*/

	// Shopify api domain
	private static $url = 'myshopify.com/admin';

	// The name of your Shopify store
	private static $name = '';

	// Api key for the private app on your store
	private static $key  = '';

	// Password for the private app on your store
	private static $pass = '';

	// Logger
	private static $log = null;

	/*
	|-----------------------------------------------------------
	| CONSTRUCTOR
	|-----------------------------------------------------------
	*/

	public function __construct( $props ) {
		foreach ( $props as $key => $value ) {
			if ( isset( Store::${$key} ) ) {
				Store::${$key} = $value;
			}
		}

		// create a log channel
		Store::$log = new Logger( 'store' );
		Store::$log->pushHandler( new StreamHandler( dirname( __FILE__ ) . '/../store.log', Logger::DEBUG ) );
	}

	/*
	|-----------------------------------------------------------
	| GETTERS
	|-----------------------------------------------------------
	*/

	public static function get_url( $append = '/' ) {
		return 'https://' . Store::$key
			. ':' . Store::$pass
			. '@' . Store::$name
			. '.' . Store::$url
			. $append;
	}

	/*
	|-----------------------------------------------------------
	| API CALLS
	|-----------------------------------------------------------
	*/

	// generic
	public static function post( $url, $data = false )   { return Store::call_api( 'POST', $url, $data ); }
	public static function put( $url, $data = false )    { return Store::call_api( 'PUT', $url, $data ); }
	public static function get( $url, $data = false )    { return Store::call_api( 'GET', $url, $data ); }
	public static function delete( $url, $data = false ) { return Store::call_api( 'DELETE', $url, $data ); }

	// call the Shopify api
	public static function call_api( $method, $url, $raw_data = array() ) {
		$url = Store::get_url( $url );

		$data = http_build_query( $raw_data );
		 
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		
		switch ( $method ) {
			case 'GET':
				$url .= '?' . $data;
				break;

			case 'DELETE':
			case 'PUT':
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
				break;
			
			case 'POST':
				curl_setopt( $ch, CURLOPT_POST, true );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
				break;
		}

		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Accept: application/json' ) );
		curl_setopt( $ch, CURLOPT_URL, $url );
		 
		$response = curl_exec($ch);
		curl_close($ch);

		Store::$log->addInfo(
			'Shopify',
			compact( 'url', 'method', 'data', 'response' )
		);

		$response = json_decode( $response );

		if ( $response ) {
			return $response;
		}

		return false;
	}

}
