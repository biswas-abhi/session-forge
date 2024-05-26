<?php

require __DIR__ . '/../vendor/autoload.php';

use SessionForge\SessionForge;
use SessionForge\SessionForgeExceptions;

class NoCompressNoEncryption
{
    public $PATH = 'sessionforge_test';
    public $FILE_NAME = 'no-compress-no-encryption';
    public $HASH_KEY = 'kwmMLcTaz1gmrdJyv3Ve1A==';
    public $TIME_OF_EXPIRE = 1816222052;

    function create ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false ];

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

    function update ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false ];

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

    function updateWithFallback ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false ];

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

    function updateWithUpdateFunctionAndFallback ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false ];

            $sessionForgeExample = new SessionForge( $config );
            $sessionForgeExample->update ( [ 'key1' => 'value1', 'key2' => 987, 'key3' => 1233.954, 'key4' => 0, 'key6' => [ '1', '232', '33' ], 'key8' => [ '1' => '1', 'red' => '2', '3' => '3' ], 'key9' => 'value8' ], true );

        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function updateFunctionAndNoFallback ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false ];

            $sessionForgeExample = new SessionForge( $config );
            $sessionForgeExample->update ( [ 'key1' => 'value1', 'key2' => 987, 'key3' => 1233.954, 'key4' => 0, 'key6' => [ '1', '232', '33' ], 'key8' => [ '1' => '1', 'red' => '2', '3' => '3' ], 'key9' => 'value8' ], false );

        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function getAllData ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false ];

            $sessionForgeExample = new SessionForge( $config );
            print_r ( $sessionForgeExample->getAll () );

        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function getDataByKey ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false ];

            $sessionForgeExample = new SessionForge( $config );
            print_r ( $sessionForgeExample->get ( 'key6' ) );

        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function delete ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false ];

            $sessionForgeExample = new SessionForge( $config );
            ( $sessionForgeExample->delete ( 'key6' ) );
            print_r ( $sessionForgeExample->get ( 'key1' ) );
            print_r ( $sessionForgeExample->get ( 'key6' ) );
        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }

    function destroy ()
    {
        try
        {
            $config = [ 'path' => $this->PATH, 'sessionId' => $this->FILE_NAME, 'hashKey' => $this->HASH_KEY, 'validUntil' => $this->TIME_OF_EXPIRE, 'compress' => false ];

            $sessionForgeExample = new SessionForge( $config );
            print_r ( $sessionForgeExample->destroy () );
            print_r ( $sessionForgeExample->get ( 'key1' ) );
        }
        catch ( SessionForgeExceptions $th )
        {
            print_r ( $th->formatMessage () );
        }
    }
}

$sessionForgeExample = new NoCompressNoEncryption();
$sessionForgeExample->create ();
$sessionForgeExample->update ();
$sessionForgeExample->updateWithFallback ();
$sessionForgeExample->updateWithUpdateFunctionAndFallback ();
$sessionForgeExample->updateFunctionAndNoFallback ();
$sessionForgeExample->getAllData ();
$sessionForgeExample->getDataByKey ();
$sessionForgeExample->delete ();
$sessionForgeExample->destroy ();
