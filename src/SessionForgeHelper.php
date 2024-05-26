<?php

/**
 * SessionForge - Session Management Library
 *
 * (c) Abhishek Biswas <biswas.abhishek105@gmail.com>
 * All rights reserved.
 */
declare (strict_types=1);

namespace SessionForge;

use Exception;
use DateTimeImmutable;
use DateTimeZone;

final class SessionForgeHelper
{
    /**
     * The `requirements` function is designed to ensure that specific PHP extensions required by SessionForge are enabled. It checks for the presence of essential extensions and throws exceptions with detailed messages if any required extension is not loaded
     * @throws SessionForgeExceptions - If Extension Not Found. It throw
     * @return void
     */
    public function requirements () : void
    {
        if ( !extension_loaded ( 'zlib' ) )
            throw new SessionForgeExceptions( 'EXTENSION_NOT_FOUND', 'Please Enable zlib Extension.', 3308 );

        if ( !extension_loaded ( 'sodium' ) )
            throw new SessionForgeExceptions( 'EXTENSION_NOT_FOUND', 'Please Enable libsodium Extension.', 1472 );

        if ( !extension_loaded ( 'json' ) )
            throw new SessionForgeExceptions( 'EXTENSION_NOT_FOUND', 'Please Enable json Extension.', 1659 );
    }

    /**
     * The `validateKey` function verifies the validity of a session key by enforcing specific criteria on its format and length. It guarantees that the provided key adheres to predetermined standards, essential for secure session management within applications.
     * @param string $key - A string representing the session key to be validated.
     * @param int $length - An integer indicating the expected length of the key in bits.
     * @return string
     * @throws SessionForgeExceptions - If the key does not meet the criteria.
     */
    public function validateKey ( string $key, int $length ) : string
    {
        /**
         * @var string|null
         */
        $decodeKey = null;

        if ( !is_string ( $key ) )
            throw new SessionForgeExceptions( 'KEY_IS_NOT_VALID', "Key must be string.", 3945 );

        if ( !$this->isHexadecimal ( $key ) )
        {
            if ( !$this->isBase64 ( $key ) )
                throw new SessionForgeExceptions( 'KEY_IS_NOT_VALID', "Key must be bin2hex or base64 encode.", 9587 );
            else
                $decodeKey = sodium_base642bin ( $key, 5 );
        }
        else
            $decodeKey = sodium_hex2bin ( $key );

        $keyLength = strlen ( $decodeKey );

        $preDefineKeyLength = $length * 8;

        if ( $keyLength !== $length )
            throw new SessionForgeExceptions( 'KEY_IS_NOT_VALID', "Key Must be {$preDefineKeyLength} bit long.", 1256 );

        return $decodeKey;
    }

    /**
     * The `isHexadecimal` function serves as a utility within the SessionForge library to determine whether a given value is represented in hexadecimal format. It's instrumental in validating the format of session keys.
     * @param string $value
     * @return bool
     */
    private function isHexadecimal ( string $value ) : bool
    {
        try
        {
            return sodium_bin2hex ( sodium_hex2bin ( $value ) ) === $value;
        }
        catch ( Exception $th )
        {
            return false;
        }
    }

    /**
     * The `isBase64` function aids in identifying whether a provided value is encoded in base64 format. It plays a crucial role in validating the format of session keys within the SessionForge library
     * @param string $value - The value to be examined for base64 encoding.
     * @return bool
     */
    private function isBase64 ( string $value ) : bool
    {
        try
        {
            return sodium_bin2base64 ( sodium_base642bin ( $value, 5 ), 5 ) === $value;
        }
        catch ( Exception $th )
        {
            return false;
        }
    }

    /**
     * Returns the preferred hash algorithm.
     * This function checks if the 'XXH3' algorithm is available, and if so, returns it. Otherwise, it defaults to 'md5'.
     *
     * @param string Algorithm
     * @return string The preferred hash algorithm. If 'XXH3' is available, it returns 'XXH3', otherwise 'md5'.
     */
    public function getHashAlgorithm ( string $algo = '' ) : string
    {
        if ( !empty ( $algo ) && in_array ( $algo, [ 'XXH3', 'md5' ] ) )
            return $algo;

        return in_array ( 'XXH3', hash_algos () ) ? 'XXH3' : 'md5';
    }

    /**
     * The `generateHmacHash` function within the SessionForge library is designed to generate an HMAC (Hash-based Message Authentication Code) hash for a given data string using a provided key. This HMAC hash is crucial for ensuring data integrity and authenticity in secure communication and session management
     * @param string $data - A string representing the data for which the HMAC hash needs to be generated
     * @param string $key - A string serving as the secret key used in the HMAC algorithm.
     * @param string $algo - HMAC algorithm.
     * @return array<string>
     */
    public function generateHmacHash ( string $data, string $key, string $algo ) : array
    {
        $hash = hash_hmac ( $algo, $data, $key );

        return [ 'hash' => $hash, "algo" => $algo ];
    }

    /**
     * The `generateNonce` function in the SessionForge library is responsible for generating a unique nonce (number used once) for use in cryptographic operations, particularly in authenticated encryption schemes. Nonces are crucial for ensuring the uniqueness and security of encrypted data.
     * @return string
     */
    public function generateNonce () : string
    {
        $nonce = random_bytes ( SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES );

        return $nonce;
    }

    /**
     * The `encryption` function in the SessionForge library facilitates the encryption of data using the ChaCha20-Poly1305 AEAD (Authenticated Encryption with Associated Data) algorithm. This encryption process ensures the confidentiality and integrity of sensitive data.
     * @param string $data - The data to be encrypted.
     * @param string $key - The encryption key used to secure the data.
     * @param string $nonce - The unique nonce used to ensure the uniqueness of the encryption operation.
     * @return string|bool
     * @throws Exception
     */
    public function encryption ( string $data, string $key, string $nonce ) : string|bool
    {
        try
        {
            $getEncryptionData = sodium_crypto_aead_chacha20poly1305_ietf_encrypt ( $data, "", $nonce, $key );
        }
        catch ( Exception $th )
        {
            return false;
        }

        return sodium_bin2hex ( $getEncryptionData );
    }

    /**
     * The `decryption` function in the SessionForge library facilitates the decryption of data encrypted using the ChaCha20-Poly1305 AEAD (Authenticated Encryption with Associated Data) algorithm. This decryption process ensures the confidentiality and integrity of sensitive data.
     * @param string $data - The data to be decrypted.
     * @param string $key - The decryption key used to decrypt the data.
     * @param string $nonce - The unique nonce used during encryption to ensure the uniqueness of the decryption operation.
     * @return string|bool - Decrypted data on success, false on failure.
     * @throws Exception - Throws an exception if decryption fails.
     */
    public function decryption ( string $data, string $key, string $nonce ) : string|bool
    {
        try
        {
            $getEncryptionData = sodium_crypto_aead_chacha20poly1305_ietf_decrypt ( sodium_hex2bin ( $data ), "", sodium_hex2bin ( $nonce ), $key );
        }
        catch ( Exception $th )
        {
            return false;
        }

        return $getEncryptionData;
    }

    /**
     * The `validateHmacHash` function in the SessionForge library is responsible for validating a given HMAC hash against a provided signature.
     * 
     * @param string $hashValue - The hash value to be validated.
     * @param string $signature - The signature against which the hash value is to be compared.
     */
    public function validateHmacHash ( string $hashValue, string $signature ) : bool
    {
        try
        {
            if ( hash_equals ( $hashValue, $signature ) )
                return true;
            else
                return false;
        }
        catch ( Exception $th )
        {
            return false;
        }
    }

    /**
     * Retrieves the current date and time as a Unix timestamp.
     *
     * @return int The Unix timestamp representing the current date and time.
     */
    public function getCurrentDateTime () : int
    {
        $getDateTime = new DateTimeZone( date_default_timezone_get () );
        $date        = new DateTimeImmutable( 'now', $getDateTime );
        $currentDate = $date->getTimestamp ();

        return $currentDate;
    }
}
