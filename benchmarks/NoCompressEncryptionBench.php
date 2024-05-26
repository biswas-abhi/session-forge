<?php

namespace SessionForge\Benchmarks;

require __DIR__ . '/../vendor/autoload.php';

use SessionForge\SessionForge;
use SessionForge\SessionForgeExceptions;

class NoCompressEncryption
{
    public $PATH = 'sessionforge_benchmark_test';
    public $FILE_NAME = 'no-compress-encryption';
    public $HASH_KEY = 'kwmMLcTaz1gmrdJyv3Ve1A==';
    public $KEY = 'ad1de17875d21d2fa8428b8832605629348c01fd0143ca4f48eac81c5bcb46bc';
    public $TIME_OF_EXPIRE = 1816222052;

    function benchCreate ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false, 'encryption' => true, 'key' => $this->KEY ];

            $sessionForgeExample = new SessionForge( $config );

            $sessionForgeExample->set ( 'key1', 'value1' ); // string
            $sessionForgeExample->set ( 'key2', 123 ); // int
            $sessionForgeExample->set ( 'key3', 123.54 ); // float
            $sessionForgeExample->set ( 'key4', 1 ); // boolean
            $sessionForgeExample->set ( 'key5', true ); // boolean
            $sessionForgeExample->set ( 'key6', [ '1', '2', '3' ] ); // array
            $sessionForgeExample->set ( 'key6', [ '1' => '1', '2' => '2', '3' => '3' ] ); // array
            $sessionForgeExample->set ( 'key7', NULL ); // null

            $sessionForgeExample->save ();
        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function benchUpdate ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false, 'encryption' => true, 'key' => $this->KEY ];

            $sessionForgeExample = new SessionForge( $config );

            $sessionForgeExample->set ( 'key1', 'value1' ); // string
            $sessionForgeExample->set ( 'key2', 987 ); // int
            $sessionForgeExample->set ( 'key3', 1233.954 ); // float
            $sessionForgeExample->set ( 'key4', 0 ); // boolean
            $sessionForgeExample->set ( 'key5', false ); // boolean
            $sessionForgeExample->set ( 'key6', [ '1', '232', '33' ] ); // array
            $sessionForgeExample->set ( 'key8', [ '1' => '1', 'red' => '2', '3' => '3' ] ); // array
            $sessionForgeExample->set ( 'key9', 'value8' ); // string

            $sessionForgeExample->save ( false );
        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function benchUpdateWithFallback ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false, 'encryption' => true, 'key' => $this->KEY ];

            $sessionForgeExample = new SessionForge( $config );

            $sessionForgeExample->set ( 'key1', 'value1' ); // string
            $sessionForgeExample->set ( 'key2', 987 ); // int
            $sessionForgeExample->set ( 'key3', 1233.954 ); // float
            $sessionForgeExample->set ( 'key4', 0 ); // boolean
            $sessionForgeExample->set ( 'key5', false ); // boolean
            $sessionForgeExample->set ( 'key6', [ '1', '232', '33' ] ); // array
            $sessionForgeExample->set ( 'key8', [ '1' => '1', 'red' => '2', '3' => '3' ] ); // array
            $sessionForgeExample->set ( 'key9', 'value8' ); // string

            $sessionForgeExample->save ( true );
        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function benchUpdateWithUpdateFunctionAndFallback ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false, 'encryption' => true, 'key' => $this->KEY ];

            $sessionForgeExample = new SessionForge( $config );
            $sessionForgeExample->update ( [ 'key1' => 'value1', 'key2' => 987, 'key3' => 1233.954, 'key4' => 0, 'key6' => [ '1', '232', '33' ], 'key8' => [ '1' => '1', 'red' => '2', '3' => '3' ], 'key9' => 'value8' ], true );

        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function benchUpdateFunctionAndNoFallback ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false, 'encryption' => true, 'key' => $this->KEY ];

            $sessionForgeExample = new SessionForge( $config );
            $sessionForgeExample->update ( [ 'key1' => 'value1', 'key2' => 987, 'key3' => 1233.954, 'key4' => 0, 'key6' => [ '1', '232', '33' ], 'key8' => [ '1' => '1', 'red' => '2', '3' => '3' ], 'key9' => 'value8' ], false );

        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }


    function benchGetAllData ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false, 'encryption' => true, 'key' => $this->KEY ];

            $sessionForgeExample = new SessionForge( $config );
            $sessionForgeExample->getAll ();

        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function benchGetDataByKey ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false, 'encryption' => true, 'key' => $this->KEY ];

            $sessionForgeExample = new SessionForge( $config );
            $sessionForgeExample->get ( 'key6' );

        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function benchDelete ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false, 'encryption' => true, 'key' => $this->KEY ];

            $sessionForgeExample = new SessionForge( $config );
            $sessionForgeExample->delete ( 'key6' );
        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function benchDestroy ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false, 'encryption' => true, 'key' => $this->KEY ];

            $sessionForgeExample = new SessionForge( $config );
            $sessionForgeExample->destroy ();
        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }
}
