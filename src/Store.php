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
	private $url = 'myshopify.com/admin';

	// The name of your Shopify store
	private $name = '';

	// Api key for the private app on your store
	private $key  = '';

	// Password for the private app on your store
	private $pass = '';

	/*
	|-----------------------------------------------------------
	| CONSTRUCTOR
	|-----------------------------------------------------------
	*/

	public function __construct( $props ) {
		foreach ( $props as $key => $value ) {
			if ( isset( $this->{$key} ) ) {
				$this->{$key} = $value;
			}
		}

		// create a log channel
		$this->log = new Logger( 'store' );
		$this->log->pushHandler( new StreamHandler( dirname( __FILE__ ) . '/../store.log', Logger::DEBUG ) );
	}

	/*
	|-----------------------------------------------------------
	| GETTERS
	|-----------------------------------------------------------
	*/

	public function get_url( $append = '/' ) {
		return 'https://' . $this->key
			. ':' . $this->pass
			. '@' . $this->name
			. '.' . $this->url
			. $append;
	}

	/*
	|-----------------------------------------------------------
	| API CALLS
	|-----------------------------------------------------------
	*/

	// generic
	public function post( $url, $data = false )   { return $this->call_api( 'POST', $url, $data ); }
	public function put( $url, $data = false )    { return $this->call_api( 'PUT', $url, $data ); }
	public function get( $url, $data = false )    { return $this->call_api( 'GET', $url, $data ); }
	public function delete( $url, $data = false ) { return $this->call_api( 'DELETE', $url, $data ); }

	// call the Shopify api
	private function call_api( $method, $url, $raw_data = false ) {
		$url = $this->get_url( $url );

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

		$this->log->addInfo(
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
