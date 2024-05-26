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

final class SessionForgeExceptions extends Exception
{
    /**
     * Session Forge Error Code Name
     *
     * @access protected
     * @var string
     */
    protected $errorName = '';

    /**
     * Session Forge Message
     *
     * @access protected
     * @var string
     */
    protected $message = '';

    /**
     * Session Forge Code
     *
     * @access protected
     * @var string
     */
    protected $code = 0;

    public function __construct ( string $errorName, string $message, int $code )
    {
        $this->errorName = $errorName;
        $this->message   = $message;
        $this->code      = $code;

        parent::__construct ( $message, $code, null );
    }

    /**
     * Retrieves the error name.
     * This method returns the name of the error that occurred.
     * 
     * @return string The name of the error.
     */
    public function getErrorName () : string
    {
        return $this->errorName;
    }

    /**
     * Formats the error message.
     *
     * This method constructs and returns a formatted error message
     * including error code, error name, and error message.
     *
     * @return string The formatted error message.
     */
    public function formatMessage () : string
    {
        return "Error: [{$this->code}] - (Error Name: {$this->errorName}) - {$this->message}";
    }
}
