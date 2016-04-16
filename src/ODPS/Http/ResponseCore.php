<?php
namespace ODPS\Http;

/**
 * Class ResponseCore
 *
 * @package ODPS\Http
 */
class ResponseCore
{
    /**
     * Stores the HTTP header information.
     */
    public $header;

    /**
     * Stores the SimpleXML response.
     */
    public $body;

    /**
     * Stores the HTTP response code.
     */
    public $status;

    /**
     * Constructs a new instance of this class.
     *
     * @param  array $header (Required) Associative array of HTTP headers (typically returned by <RequestCore::get_response_header()>).
     * @param  string $body (Required) XML-formatted response from AWS.
     * @param  integer $status (Optional) HTTP response status code from the request.
     * @return Mixed Contains an <php:array> `header` property (HTTP headers as an associative array), a <php:SimpleXMLElement> or <php:string> `body` property, and an <php:integer> `status` code.
     */
    public function __construct($header, $body, $status = null)
    {
        $this->header = $header;
        $this->body = $body;

        $this->body = $body;

        $this->status = $status;

        if ($status < 400) {
            $this->setResponseObjectProperties($header, $body);
        }

        return $this;
    }

    /**
     * Did we receive the status code we expected?
     *
     * @param  integer|array $codes (Optional) The status code(s) to expect. Pass an <php:integer> for a single acceptable value, or an <php:array> of integers for multiple acceptable values.
     * @return boolean Whether we received the expected status code or not.
     */
    public function isOK($codes = array(200, 201, 204, 206))
    {
        if (is_array($codes)) {
            return in_array($this->status, $codes);
        }

        return $this->status === $codes;
    }

    private function setResponseObjectProperties($header, $body)
    {
        try {
            $responseObj = null;
            if (array_key_exists("content-type", $header)) {
                if ($header["content-type"] === "application/xml") {
                    $responseObj = simplexml_load_string($body);
                } else if ($header["content-type"] === "application/json") {
                    $responseObj = json_decode($body);
                }

                if ($responseObj) {
                    foreach (get_object_vars($responseObj) as $key => $value) {
                        $this->$key = $value;
                    }
                }
            }
        } catch (\Exception $error) {
            echo "Response body is not match with the content-type.";
        }
    }
}
