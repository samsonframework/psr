<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 23.08.2015
 * Time: 13:48
 */
namespace samsonframework\psr;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use samsonframework\psr\exception\ProtocolVersionNotSupported;

class Message implements \Psr\Http\Message\MessageInterface
{
    /** @var string HTTP protocol version */
    protected $protocolVersion = '1.0';

    /** @var \Psr\Http\Message\StreamInterface HTTP message body */
    protected $body;

    /**
     * @var array HTTP message headers collection, keys are case-insensitive(lowercase),
     * array[0] - original header with casing, array[1] - collection of header values
     */
    protected $headers = array();

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     * @return self
     */
    public function withProtocolVersion($version)
    {
        // Create new HTTP message
        $message = clone $this;
        $message->protocolVersion = $version;
        return $message;
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return array Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders()
    {
        // Reorganize array with original set case-sensitive header names
        $result = array();
        foreach ($this->headers as $key => $data) {
            $result[$data[0]] = $data[1];
        }
        return $result;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        return null !== $this->getHeaderItem($name);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name)
    {
        // Try to point to searched header by name
        $pointer = $this->getHeaderItem($name);

        if (isset($pointer)) {
            return $pointer[1];
        }

        return array();
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name)
    {
        // Try to point to searched header by name
        $pointer = $this->getHeaderItem($name);

        if (isset($pointer)) {
            // Concatenate values with comma
            return implode(',', $pointer[1]);
        }

        return '';
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        // Create new message clone
        $newMessage = clone $this;

        // Cast value to array
        $value = is_array($value) ? $value : array($value);

        // Store header by case-insensitive name, add array with original casing name and values collection
        $newMessage->headers[strtolower($name)] = array($name, $value);

        // Chaining
        return $newMessage;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value)
    {
        // Create new message clone
        $newMessage = clone $this;

        // Get pointer to message header element
        $header = & $newMessage->getHeaderItem($name);

        // There were no such header before
        if ($header === null) {
            return $newMessage->withHeader($name, $value);
        } else { // Header already exists - merger value arrays with value array casting
            $header[1] = array_merge($header[1], is_array($value) ? $value : array($value));
        }

        // Chaining
        return $newMessage;
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return self
     */
    public function withoutHeader($name)
    {
        // Create new message clone
        $newMessage = clone $this;

        // Remove header element
        unset($newMessage->headers[strtolower($name)]);

        return $newMessage;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return self
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        // Create new message clone
        $newMessage = clone $this;

        $this->body = $body;

        return $newMessage;
    }

    /**
     * Retrieve header data item with case-insensitive header name
     * @param string $name Case-insesitive header name
     * @return array Header data item(0=>CASE_SENSITIVE_NAME, 1=>ARRAY_OF_VALUES)
     */
    private function & getHeaderItem($name)
    {
        // Try to point to searched header by name
        return $this->headers[strtolower($name)];
    }
}
