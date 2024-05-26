<?php

/**
 * SessionForge - Session Management Library
 *
 * (c) Abhishek Biswas <biswas.abhishek105@gmail.com>
 * All rights reserved.
 */
declare (strict_types=1);

namespace SessionForge;

use ReflectionProperty;
use Exception;

final class SessionForge
{
    /**
     * Store Path of Session
     *
     * @access public
     * @var string|null
     */
    public $path = null;

    /**
     * File Name of Session
     *
     * @access public
     * @var string|null
     */
    public $sessionId = null;

    /**
     * Store Data Format
     *
     * @access private
     * @var string
     */
    private $fileType = "json";

    /**
     * Session Value
     *
     * @access public
     * @var array|list
     */
    public $value = [];

    /**
     * Hash Key
     *
     * @access public
     * @var string|null
     */
    public $hashKey = null;

    /**
     * Encryption
     *
     * @access public
     * @var bool
     */
    public $encryption = false;

    /**
     * Encryption Key
     *
     * @access public
     * @var string
     */
    public $key = null;

    /**
     * Valid Until
     *
     * @access public
     * @var int|null
     */
    public $validUntil = null;

    /**
     * Version Of Session Forge
     *
     * @access public
     * @var float
     */
    public const VERSION = 1.0;

    /**
     * Set Data
     *
     * @access private
     * @var array|list
     */
    private $SET_DATA = [];

    /**
     * Is Compress
     *
     * @access public
     * @var bool
     */
    public $compress = true;

    /**
     * Compress Level
     *
     * @access private
     * @var int
     */
    private const COMPRESS_LEVEL = 9;

    /**
     * Compress Encoding
     *
     * @access private
     * @var int
     */
    private const COMPRESS_ENCODING = ZLIB_ENCODING_GZIP;

    public function __construct ( array $config = [] )
    {
        $this->initialize ( $config );
    }

    /**
     * Initialize preferences
     *
     * @param array $config
     * @return SessionForge
     */
    private function initialize ( array $config ) : static
    {
        $helper = new SessionForgeHelper();

        $helper->requirements ();

        // Override Default Public Property
        foreach ( array_keys ( get_class_vars ( static::class) ) as $key )
        {
            if ( property_exists ( static::class, $key ) && isset ( $config[ $key ] ) )
            {
                $reflectionProperty = new ReflectionProperty( static::class, $key );

                if ( $reflectionProperty->isPublic () )
                    $this->{$key} = $config[ $key ];
            }
        }

        // Check User Give File Path or not
        if ( empty ( $this->path ) )
            throw new SessionForgeExceptions( 'PATH_NOT_FOUND', 'Please define Session Path.', 8514 );

        // Check Folder Exist or Not
        if ( !file_exists ( $this->path ) )
            if ( !mkdir ( $this->path, 0755, true ) )
                throw new SessionForgeExceptions( 'PERMISSION_ISSUE', 'Folder creation failed. Check permissions or other issues.', 1818 );

        // Check Folder is read and write permission
        if ( !is_writeable ( $this->path ) || !is_readable ( $this->path ) )
            throw new SessionForgeExceptions( 'PERMISSION_ISSUE', 'Insufficient permissions to read/write file. Please grant appropriate permissions.', 5943 );

        // Check Filename is not Empty
        if ( empty ( $this->sessionId ) )
            throw new SessionForgeExceptions( 'ID_SHOULD_NOT_EMPTY', 'Provided ID is not unique or valid for use as a file name.', 4598 );

        // Check Hash Key is not Empty
        if ( empty ( $this->hashKey ) )
            throw new SessionForgeExceptions( 'KEY_NOT_FOUND', 'Please Provide Hash Key.', 3975 );
        else
            $this->hashKey = $helper->validateKey ( $this->hashKey, 16 );

        // If Encryption Enable
        if ( $this->encryption )
        {
            if ( empty ( $this->key ) )
                throw new SessionForgeExceptions( 'KEY_NOT_FOUND', 'Please Provide Encryption Key.', 4955 );
            else
                $this->key = $helper->validateKey ( $this->key, 32 );
        }

        if ( empty ( $this->validUntil ) )
            throw new SessionForgeExceptions( 'SESSION_TIME_NOT_FOUND', 'Please specify the session end time.', 2854 );

        return $this;
    }

    /**
     * Set Value with key-value pair
     *
     * @param string|int $key
     * @param mixed $value
     * @return SessionForge
     * @throws SessionForgeExceptions
     */
    public function set ( string|int $key, mixed $value ) : static
    {
        if ( empty ( $key ) || !isset ( $key ) )
            throw new SessionForgeExceptions( 'ARRAY_KEY_NOT_FOUND', 'Please Provide Key Name.', 4984 );

        $this->value[ $key ] = $value;

        return $this;
    }

    /**
     * Save Data into filesystem
     *
     * @param bool $fallback - If the old session file is corrupt, the system takes a backup before writing a new file. otherwise, it replaces the old data.
     * @return SessionForge
     */
    public function save ( bool $fallback = false ) : static
    {
        $sessionFile = "{$this->path}/{$this->sessionId}";

        if ( !file_exists ( $sessionFile ) )
        {
            if ( $this->fileType == 'json' )
                $this->sessionStoreInJson ( $sessionFile, $fallback, false );
        }
        else
            if ( $this->fileType == 'json' )
                $this->sessionStoreInJson ( $sessionFile, $fallback, true );

        return $this;
    }

    /**
     * The `update` function is responsible for updating session data stored in files with new information provided in the form of an array. It ensures that session data remains current and accurate throughout the application's lifecycle.
     *
     * @param array $data -  An array containing the updated session data to be stored.
     * @param bool $fallback - A boolean parameter indicating whether to fallback to a default behavior if saving fails.
     * @return SessionForge
     * @throws SessionForgeExceptions
     */
    public function update ( array $data, bool $fallback = false ) : static
    {
        if ( !is_array ( $data ) )
            throw new SessionForgeExceptions( 'DATA_NOT_FOUND', 'Please Provide array.', 5523 );

        $this->value = $data;

        $this->save ( $fallback );

        return $this;
    }

    /**
     * The `getAll` function retrieves session data from a file and performs validation checks before returning the data.
     * @return array
     * @throws SessionForgeExceptions
     */
    public function getAll () : array
    {
        $helper = new SessionForgeHelper();

        /**
         * @var string
         */
        $getSessionData = '';

        try
        {
            $sessionFile = "{$this->path}/{$this->sessionId}";

            if ( !file_exists ( $sessionFile ) )
                throw new SessionForgeExceptions( 'SESSIONFORGE_NOT_INITIALIZE', "Initialize SessionForge before using it.", 3460 );

            $getSessionData = $this->compress ? @gzdecode ( file_get_contents ( $sessionFile ) ) : file_get_contents ( $sessionFile );
        }
        catch ( Exception $th )
        {
            throw new SessionForgeExceptions( 'SESSIONFORGE_NOT_INITIALIZE', "Initialize SessionForge before using it.", 3460 );
        }

        if ( !is_bool ( $getSessionData ) && !empty ( $getSessionData ) )
        {
            $getSessionData = json_decode ( $getSessionData, true );

            if ( $this->encryption )
            {
                $getDecryptData = $helper->decryption ( $getSessionData[ 'data' ], $this->key, $getSessionData[ 'nonce' ] );

                if ( $getDecryptData && !empty ( $getDecryptData ) )
                    $checkHashData[ 'data' ] = json_decode ( $getDecryptData, true );
                else
                    $checkHashData[ 'data' ] = [];
            }
            else
                $checkHashData[ 'data' ] = $getSessionData[ 'data' ] ?? [];

            $checkHashData[ 'version' ]    = $getSessionData[ 'version' ] ?? 0;
            $checkHashData[ 'validUntil' ] = $getSessionData[ 'validUntil' ] ?? 0;
            $getAlgorithm                  = $helper->getHashAlgorithm ( $getSessionData[ 'algo' ] );

            $getHashData = $helper->generateHmacHash ( json_encode ( $checkHashData ), $this->hashKey, $getAlgorithm );

            $isValidate = $helper->validateHmacHash ( $getHashData[ 'hash' ], $getSessionData[ 'hash' ] ?? "" );

            if ( $isValidate )
            {
                $this->SET_DATA = [];

                $getHashData = $helper->generateHmacHash ( json_encode ( $this->SET_DATA ), $this->hashKey, $getAlgorithm );

                $currentDate = $helper->getCurrentDateTime ();

                if ( $currentDate > $checkHashData[ 'validUntil' ] )
                    throw new SessionForgeExceptions( 'SESSION_EXPIRE', 'The session has expired.', 9022 );

                return $checkHashData[ 'data' ];
            }
        }

        return [];
    }

    /**
     * The `get` function retrieves session data from a file and performs validation checks before returning the data.
     * @param string|int $key
     * @return mixed
     * @throws SessionForgeExceptions
     */
    public function get ( string|int $key ) : mixed
    {
        $getData = $this->getAll ();
        if ( isset ( $getData[ $key ] ) )
            return $getData[ $key ];

        return '';
    }

    /**
     * Deletes a session variable identified by the provided key.
     *
     * @param string $key The key of the session variable to delete.
     * @return SessionForge Returns the SessionForge object for method chaining.
     */
    public function delete ( string $key ) : static
    {
        $helper = new SessionForgeHelper();

        try
        {
            $sessionFile = "{$this->path}/{$this->sessionId}";

            if ( !file_exists ( $sessionFile ) )
                throw new SessionForgeExceptions( 'SESSIONFORGE_NOT_INITIALIZE', "Initialize SessionForge before using it.", 3460 );

            $getSessionData = $this->compress ? @gzdecode ( file_get_contents ( $sessionFile ) ) : file_get_contents ( $sessionFile );
        }
        catch ( Exception $th )
        {
            throw new SessionForgeExceptions( 'SESSIONFORGE_NOT_INITIALIZE', "Initialize SessionForge before using it.", 3460 );
        }

        if ( !is_bool ( $getSessionData ) && !empty ( $getSessionData ) )
        {
            $getSessionData = json_decode ( $getSessionData, true );

            if ( $this->encryption )
            {

                $getDecryptData = $helper->decryption ( $getSessionData[ 'data' ], $this->key, $getSessionData[ 'nonce' ] );

                if ( $getDecryptData && !empty ( $getDecryptData ) )
                    $checkHashData[ 'data' ] = json_decode ( $getDecryptData, true );
                else
                    $checkHashData[ 'data' ] = [];
            }
            else
                $checkHashData[ 'data' ] = $getSessionData[ 'data' ] ?? [];

            $checkHashData[ 'version' ]    = $getSessionData[ 'version' ] ?? "";
            $checkHashData[ 'validUntil' ] = $getSessionData[ 'validUntil' ] ?? 0;
            $getAlgorithm                  = $helper->getHashAlgorithm ( $getSessionData[ 'algo' ] );

            $getHashData = $helper->generateHmacHash ( json_encode ( $checkHashData ), $this->hashKey, $getAlgorithm );

            $isValidate = $helper->validateHmacHash ( $getHashData[ 'hash' ], $getSessionData[ 'hash' ] ?? "" );

            if ( $isValidate )
            {
                $this->SET_DATA = [];

                $this->value = array_merge ( $checkHashData[ 'data' ], $this->value );

                $this->setStoreData ( $this->value );

                $getHashData = $helper->generateHmacHash ( json_encode ( $this->SET_DATA ), $this->hashKey, $getAlgorithm );

                $currentDate = $helper->getCurrentDateTime ();

                if ( $currentDate > $checkHashData[ 'validUntil' ] )
                    throw new Exception( 'Session has been Expire.', 9022 );

                if ( isset ( $checkHashData[ 'data' ][ $key ] ) )
                    unset ( $checkHashData[ 'data' ][ $key ] );

                $this->value = $checkHashData[ 'data' ];

                $this->SET_DATA = [];

                $this->setStoreData ( $this->value );

                $getAlgorithm = $helper->getHashAlgorithm ( $getSessionData[ 'algo' ] );

                $getHashData = $helper->generateHmacHash ( json_encode ( $this->SET_DATA ), $this->hashKey, $getAlgorithm );

                if ( $this->encryption )
                {
                    $nonce                     = $helper->generateNonce ();
                    $this->value               = $helper->encryption ( json_encode ( $this->value ), $this->key, $nonce );
                    $this->SET_DATA[ 'nonce' ] = sodium_bin2hex ( $nonce );

                    $this->setStoreData ( $this->value );
                }

                $this->SET_DATA[ 'hash' ] = $getHashData[ 'hash' ];
                $this->SET_DATA[ 'algo' ] = $getHashData[ 'algo' ];

                $writeData = $this->compress ? @gzcompress ( json_encode ( $this->SET_DATA ), self::COMPRESS_LEVEL, self::COMPRESS_ENCODING ) : json_encode ( $this->SET_DATA );

                file_put_contents ( $sessionFile, $writeData );
            }

        }
        return $this;
    }

    /**
     * Destroys the session file associated with the current SessionForge instance.
     *
     * @throws SessionForgeExceptions If SessionForge is not initialized or encounters an error during the process.
     * @return SessionForge Returns the current SessionForge instance.
     */
    public function destroy () : static
    {
        $helper = new SessionForgeHelper();

        try
        {
            $sessionFile = "{$this->path}/{$this->sessionId}";

            if ( !file_exists ( $sessionFile ) )
            {
                throw new SessionForgeExceptions( 'SESSIONFORGE_NOT_INITIALIZE', "Initialize SessionForge before using it.", 3460 );
            }

            $getSessionData = $this->compress ? @gzdecode ( file_get_contents ( $sessionFile ) ) : file_get_contents ( $sessionFile );
        }
        catch ( Exception $th )
        {
            throw new SessionForgeExceptions( 'SESSIONFORGE_NOT_INITIALIZE', "Initialize SessionForge before using it.", 3460 );
        }

        if ( !is_bool ( $getSessionData ) && !empty ( $getSessionData ) )
        {
            $getSessionData = json_decode ( $getSessionData, true );

            if ( $this->encryption )
            {

                $getDecryptData = $helper->decryption ( $getSessionData[ 'data' ], $this->key, $getSessionData[ 'nonce' ] );

                if ( $getDecryptData && !empty ( $getDecryptData ) )
                    $checkHashData[ 'data' ] = json_decode ( $getDecryptData, true );
                else
                    $checkHashData[ 'data' ] = [];

            }
            else
                $checkHashData[ 'data' ] = $getSessionData[ 'data' ] ?? [];

            $checkHashData[ 'version' ]    = $getSessionData[ 'version' ] ?? "";
            $checkHashData[ 'validUntil' ] = $getSessionData[ 'validUntil' ] ?? 0;
            $getAlgorithm                  = $helper->getHashAlgorithm ( $getSessionData[ 'algo' ] );

            $getHashData = $helper->generateHmacHash ( json_encode ( $checkHashData ), $this->hashKey, $getAlgorithm );

            $isValidate = $helper->validateHmacHash ( $getHashData[ 'hash' ], $getSessionData[ 'hash' ] ?? "" );

            if ( $isValidate )
            {
                $this->SET_DATA = [];

                $getHashData = $helper->generateHmacHash ( json_encode ( $this->SET_DATA ), $this->hashKey, $getAlgorithm );

                if ( !unlink ( $sessionFile ) )
                    rename ( $sessionFile, $sessionFile . "_" . time () );
            }
        }

        return $this;
    }

    /**
     * The `sessionStoreInJson` function facilitates storing session data in JSON format. Depending on the provided parameters, it either writes the session data to a file or merges it with existing data in the file.
     *
     * @param string $sessionFile - Path to the file where session data will be stored.
     * @param bool $fallback - Indicates whether to create a backup of the existing file before writing new session data.
     * @param bool $overWrite - Indicates whether to overwrite existing session data in the file.
     * @return SessionForge
     */
    private function sessionStoreInJson ( string $sessionFile, bool $fallback, bool $overWrite ) : static
    {
        $helper = new SessionForgeHelper();

        if ( !$overWrite )
        {
            $this->SET_DATA = [];

            $this->setStoreData ( $this->value );

            $algo        = $helper->getHashAlgorithm ();
            $getHashData = $helper->generateHmacHash ( json_encode ( $this->SET_DATA ), $this->hashKey, $algo );

            $this->SET_DATA[ 'hash' ] = $getHashData[ 'hash' ];
            $this->SET_DATA[ 'algo' ] = $getHashData[ 'algo' ];

            if ( $this->encryption )
            {
                $nonce                     = $helper->generateNonce ();
                $this->value               = $helper->encryption ( json_encode ( $this->value ), $this->key, $nonce );
                $this->SET_DATA[ 'nonce' ] = sodium_bin2hex ( $nonce );

                $this->setStoreData ( $this->value );
            }

            // Store data
            $this->compress ? file_put_contents ( $sessionFile, @gzcompress ( json_encode ( $this->SET_DATA ), self::COMPRESS_LEVEL, self::COMPRESS_ENCODING ) ) : file_put_contents ( $sessionFile, json_encode ( $this->SET_DATA ) );
        }
        else
        {
            $getSessionData = $this->compress ? @gzdecode ( file_get_contents ( $sessionFile ) ) : file_get_contents ( $sessionFile );

            if ( !is_bool ( $getSessionData ) && !empty ( $getSessionData ) )
            {
                $getSessionData = json_decode ( $getSessionData, true );

                /**
                 * @var array|list
                 */
                $checkHashData = [];

                if ( $this->encryption )
                {
                    $getDecryptData = $helper->decryption ( $getSessionData[ 'data' ], $this->key, $getSessionData[ 'nonce' ] );

                    if ( $getDecryptData && !empty ( $getDecryptData ) )
                        $checkHashData[ 'data' ] = json_decode ( $getDecryptData, true );
                    else
                        $checkHashData[ 'data' ] = [];
                }
                else
                    $checkHashData[ 'data' ] = $getSessionData[ 'data' ] ?? [];

                $checkHashData[ 'version' ]    = $getSessionData[ 'version' ] ?? 0;
                $checkHashData[ 'validUntil' ] = $getSessionData[ 'validUntil' ] ?? 0;
                $getAlgorithm                  = $helper->getHashAlgorithm ( $getSessionData[ 'algo' ] );

                $getHashData = $helper->generateHmacHash ( json_encode ( $checkHashData ), $this->hashKey, $getAlgorithm );

                $isValidate = $helper->validateHmacHash ( $getHashData[ 'hash' ], $getSessionData[ 'hash' ] ?? "" );

                if ( $isValidate )
                {
                    $this->SET_DATA = [];
                    $this->value    = array_merge ( $checkHashData[ 'data' ], $this->value );

                    $this->setStoreData ( $this->value );

                    $getHashData = $helper->generateHmacHash ( json_encode ( $this->SET_DATA ), $this->hashKey, $getAlgorithm );

                    if ( $this->encryption )
                    {
                        $nonce                     = $helper->generateNonce ();
                        $this->value               = $helper->encryption ( json_encode ( $this->value ), $this->key, $nonce );
                        $this->SET_DATA[ 'nonce' ] = sodium_bin2hex ( $nonce );

                        $this->setStoreData ( $this->value );
                    }

                    $this->SET_DATA[ 'hash' ] = $getHashData[ 'hash' ];
                    $this->SET_DATA[ 'algo' ] = $getHashData[ 'algo' ];

                    $currentDate = $helper->getCurrentDateTime ();

                    if ( $currentDate > $checkHashData[ 'validUntil' ] )
                        throw new SessionForgeExceptions( 'SESSION_EXPIRE', 'The session has expired.', 9022 );

                    $writeData = $this->compress ? @gzcompress ( json_encode ( $this->SET_DATA ), self::COMPRESS_LEVEL, self::COMPRESS_ENCODING ) : json_encode ( $this->SET_DATA );

                    file_put_contents ( $sessionFile, $writeData );

                    return $this;

                }
            }

            $this->SET_DATA = [];

            $this->setStoreData ( $this->value );

            $algo        = $helper->getHashAlgorithm ();
            $getHashData = $helper->generateHmacHash ( json_encode ( $this->SET_DATA ), $this->hashKey, $algo );

            if ( $this->encryption )
            {
                $nonce                     = $helper->generateNonce ();
                $this->value               = $helper->encryption ( json_encode ( $this->value ), $this->key, $nonce );
                $this->SET_DATA[ 'nonce' ] = sodium_bin2hex ( $nonce );
                $this->setStoreData ( $this->value );
            }

            $this->SET_DATA[ 'hash' ] = $getHashData[ 'hash' ];
            $this->SET_DATA[ 'algo' ] = $getHashData[ 'algo' ];

            if ( $fallback )
                rename ( $sessionFile, $sessionFile . "_" . time () );

            // Store data
            $this->compress ? file_put_contents ( $sessionFile, @gzcompress ( json_encode ( $this->SET_DATA ), self::COMPRESS_LEVEL, self::COMPRESS_ENCODING ) ) : file_put_contents ( $sessionFile, json_encode ( $this->SET_DATA ) );
        }

        return $this;
    }

    /**
     * The `setStoreData` function in the SessionForge library is responsible for storing session data along with associated hashing information. It encapsulates the process of setting data into the session store with necessary metadata such as version. 
     * @param array<mixed>|string $data - A mixed type variable representing the data to be stored in the session.
     * @return SessionForge
     */
    private function setStoreData ( array|string $data ) : static
    {
        $this->SET_DATA[ 'data' ]       = $data;
        $this->SET_DATA[ 'version' ]    = self::VERSION;
        $this->SET_DATA[ 'validUntil' ] = $this->validUntil;

        return $this;
    }
}
