<?php

namespace ODPS\Core;

use \ODPS\Http\RequestCore;
use \ODPS\Http\ResponseCore;

/**
 * Class OdpsClient for store the authentication info
 *
 * @package ODPS\Core
 */
class OdpsClient
{
    const ODPS_LIFECYCLE_EXPIRATION = "Expiration";
    const ODPS_LIFECYCLE_TIMING_DAYS = "Days";
    const ODPS_LIFECYCLE_TIMING_DATE = "Date";
    const ODPS_HEADERS = OdpsUtil::ODPS_HEADERS;
    const ODPS_CURR_PROJECT = 'curr_project';
    const ODPS_METHOD = 'method';
    const ODPS_QUERY = 'query';
    const ODPS_BASENAME = 'basename';
    const ODPS_MAX_KEYS = 'max-keys';
    const ODPS_UPLOAD_ID = 'uploadId';
    const ODPS_PART_NUM = 'partNumber';
    const ODPS_CNAME_COMP = 'comp';
    const ODPS_MAX_KEYS_VALUE = 100;
    const ODPS_MAX_OBJECT_GROUP_VALUE = OdpsUtil::ODPS_MAX_OBJECT_GROUP_VALUE;

    const ODPS_MAX_PART_SIZE = OdpsUtil::ODPS_MAX_PART_SIZE;
    const ODPS_MID_PART_SIZE = OdpsUtil::ODPS_MID_PART_SIZE;
    const ODPS_MIN_PART_SIZE = OdpsUtil::ODPS_MIN_PART_SIZE;

    const ODPS_FILE_SLICE_SIZE = 8192;
    const ODPS_PREFIX = 'prefix';
    const ODPS_DELIMITER = 'delimiter';
    const ODPS_MARKER = 'marker';
    const ODPS_CONTENT_MD5 = 'Content-Md5';
    const ODPS_SELF_CONTENT_MD5 = 'x-odps-meta-md5';
    const ODPS_CONTENT_TYPE = 'Content-Type';
    const ODPS_CONTENT_LENGTH = 'Content-Length';
    const ODPS_IF_MODIFIED_SINCE = 'If-Modified-Since';
    const ODPS_IF_UNMODIFIED_SINCE = 'If-Unmodified-Since';
    const ODPS_IF_MATCH = 'If-Match';
    const ODPS_IF_NONE_MATCH = 'If-None-Match';
    const ODPS_CACHE_CONTROL = 'Cache-Control';
    const ODPS_EXPIRES = 'Expires';
    const ODPS_PREAUTH = 'preauth';
    const ODPS_RESOURCE = 'odpsResource';
    const ODPS_CONTENT_COING = 'Content-Coding';
    const ODPS_CONTENT_DISPOSTION = 'Content-Disposition';
    const ODPS_RANGE = 'range';
    const ODPS_ETAG = 'etag';
    const ODPS_LAST_MODIFIED = 'lastmodified';
    const OS_CONTENT_RANGE = 'Content-Range';
    const ODPS_CONTENT = OdpsUtil::ODPS_CONTENT;
    const ODPS_BODY = 'body';
    const ODPS_LENGTH = OdpsUtil::ODPS_LENGTH;
    const ODPS_HOST = 'Host';
    const ODPS_DATE = 'Date';
    const ODPS_AUTHORIZATION = 'Authorization';
    const ODPS_FILE_DOWNLOAD = 'fileDownload';
    const ODPS_FILE_UPLOAD = 'fileUpload';
    const ODPS_PART_SIZE = 'partSize';
    const ODPS_SEEK_TO = 'seekTo';
    const ODPS_SIZE = 'size';
    const ODPS_QUERY_STRING = 'query_string';
    const ODPS_SUB_RESOURCE = 'sub_resource';
    const ODPS_DEFAULT_PREFIX = 'x-odps-';
    const ODPS_CHECK_MD5 = 'checkmd5';
    const DEFAULT_CONTENT_TYPE = 'application/octet-stream';
    const ODPS_ENDPOINT = "odpsEndpoint";
    const ODPS_PARAMS = "odpsParams";
    const ODPS_URL_ACCESS_KEY_ID = 'OSSAccessKeyId';
    const ODPS_URL_EXPIRES = 'Expires';
    const ODPS_URL_SIGNATURE = 'Signature';
    const ODPS_HTTP_GET = 'GET';
    const ODPS_HTTP_PUT = 'PUT';
    const ODPS_HTTP_HEAD = 'HEAD';
    const ODPS_HTTP_POST = 'POST';
    const ODPS_HTTP_DELETE = 'DELETE';
    const ODPS_HTTP_OPTIONS = 'OPTIONS';
    const ODPS_ACL = 'x-odps-acl';
    const ODPS_OBJECT_ACL = 'x-odps-object-acl';
    const ODPS_OBJECT_GROUP = 'x-odps-file-group';
    const ODPS_MULTI_PART = 'uploads';
    const ODPS_MULTI_DELETE = 'delete';


    const ODPS_OBJECT_COPY_SOURCE = 'x-odps-copy-source';
    const ODPS_OBJECT_COPY_SOURCE_RANGE = "x-odps-copy-source-range";
    const ODPS_USER_AGENT = "x-odps-user-agent";

    const ODPS_ACL_TYPE_PRIVATE = 'private';
    const ODPS_ACL_TYPE_PUBLIC_READ = 'public-read';
    const ODPS_ACL_TYPE_PUBLIC_READ_WRITE = 'public-read-write';
    const ODPS_ENCODING_TYPE = "encoding-type";
    const ODPS_ENCODING_TYPE_URL = "url";
    const  TransferEncoding = "Transfer-Encoding";
    const ODPS_HOST_TYPE_NORMAL = "normal";

    const ODPS_HOST_TYPE_IP = "ip";
    const ODPS_HOST_TYPE_SPECIAL = 'special';
    const ODPS_HOST_TYPE_CNAME = "cname";
    const ODPS_NAME = "aliyun-odps-sdk-php";
    const ODPS_VERSION = "1.0.0";
    const ODPS_BUILD = "20160212";
    const ODPS_AUTHOR = "";

    const ODPS_OPTIONS_ORIGIN = 'Origin';
    const ODPS_OPTIONS_REQUEST_METHOD = 'Access-Control-Request-Method';
    const ODPS_OPTIONS_REQUEST_HEADERS = 'Access-Control-Request-Headers';
    const XmlContentType = "application/xml";
    static $ODPS_ACL_TYPES = array(
        self::ODPS_ACL_TYPE_PRIVATE,
        self::ODPS_ACL_TYPE_PUBLIC_READ,
        self::ODPS_ACL_TYPE_PUBLIC_READ_WRITE
    );
    private $maxRetries = 3;

    private $redirects = 0;//http://bucket.odps-cn-hangzhou.aliyuncs.com/object
    private $requestUrl; //http://bucket.guizhou.gov/object
    private $accessKeyId;  //http://mydomain.com/object
    private $accessKeySecret;

    private $timeout = 0;
    private $connectTimeout = 0;
    private $endPoint;
    private $defaultProject;
    private $bySocket;
    private $debug;
    private $httpProxy;

    /**
     * @param string $accessKeyId
     * @param string $accessKeySecret
     * @param null $endpoint
     * @param null $defaultProject
     * @throws OdpsException
     */
    public function __construct($accessKeyId, $accessKeySecret, $endpoint = null, $defaultProject = null, $debug = false)
    {
        $bySocket = false;
        if (empty($accessKeyId)) {
            throw new OdpsException("access key id is empty");
        }
        if (empty($accessKeySecret)) {
            throw new OdpsException("access key secret is empty");
        }
        if (empty($endpoint)) {
            throw new OdpsException("endpoint is empty");
        }

        if (empty($defaultProject)) {
            throw new OdpsException("project is empty");
        }

        $this->endPoint = $this->verifyEndpoint($endpoint);
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->defaultProject = $defaultProject;
        $this->_makeSureCurlEnabled();
        $this->bySocket = $bySocket;
        $this->debug = $debug;

        if ((defined("SetOpensearchServiceCallHttpProxy") && constant("SetOpensearchServiceCallHttpProxy") !== "")) {
            $this->setHttpProxy(constant("SetOpensearchServiceCallHttpProxy"));
        }
    }

    /**
     * @return mixed
     */
    public function getHttpProxy()
    {
        return $this->httpProxy;
    }

    /**
     * @param mixed $httpProxy
     */
    public function setHttpProxy($httpProxy)
    {
        $this->httpProxy = $httpProxy;
    }

    public function setDebugMode($debug = true)
    {
        $this->debug = $debug;
    }

    public function getDebugMode()
    {
        return $this->debug;
    }

    /**
     * Get default project
     *
     * @return null
     */
    public function getDefaultProject()
    {
        return $this->defaultProject;
    }

    /**
     * Set default project
     *
     * @param null $defaultProject
     */
    public function setDefaultProject($defaultProject)
    {
        if (empty($defaultProject)) {
            throw new OdpsException("project is empty");
        }

        $this->defaultProject = $defaultProject;
    }

    /**
     * Get endpoint
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endPoint;
    }

    /**
     * Set endpoint
     *
     * @param string $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $this->verifyEndpoint($endpoint);
    }

    /**
     * @param $options
     * @param null $currProject
     * @return ResponseCore
     * @throws OdpsException
     * @throws \ODPS\Http\RequestCore_Exception
     */
    public function call($baseUrl, $options, $currProject = null)
    {
        if ($currProject != null && !empty($currProject)) {
            if (isset($options[OdpsClient::ODPS_PARAMS])) {
                $options[OdpsClient::ODPS_PARAMS] = array_merge(array(), $options[OdpsClient::ODPS_PARAMS]);
            } else {
                $options[OdpsClient::ODPS_PARAMS] = array();
            }

            $options[OdpsClient::ODPS_PARAMS][OdpsClient::ODPS_CURR_PROJECT] = $currProject;
        }

        $signable_resource = $this->generateSignableResource($options);

        $signable_query_string_params = $this->generateSignableQueryStringParam($options);
        $signable_query_string = OdpsUtil::toQueryString($signable_query_string_params, $options);

        $resource_uri = $this->generateSignableResource($options);

        $conjunction = '?';
        $non_signable_resource = '';

        if ($signable_query_string !== '') {
            $signable_query_string = $conjunction . $signable_query_string;
            $conjunction = '&';
        }

        $query_string = $this->generateQueryString($options);
        if ($query_string !== '') {
            $non_signable_resource .= $conjunction . $query_string;
        }

        $this->requestUrl = ($baseUrl != null && !empty($baseUrl) ? $baseUrl : $this->_getAccessUrl($options)) . $resource_uri . $signable_query_string . $non_signable_resource;

        $request = new RequestCore($this->requestUrl, $this->httpProxy, null, $this->bySocket);

        $request->set_useragent($this->generateUserAgent());

        // Streaming uploads
        if (isset($options[self::ODPS_FILE_UPLOAD])) {
            if (is_resource($options[self::ODPS_FILE_UPLOAD])) {
                $length = null;

                if (isset($options[self::ODPS_CONTENT_LENGTH])) {
                    $length = $options[self::ODPS_CONTENT_LENGTH];
                } elseif (isset($options[self::ODPS_SEEK_TO])) {
                    $stats = fstat($options[self::ODPS_FILE_UPLOAD]);
                    if ($stats && $stats[self::ODPS_SIZE] >= 0) {
                        $length = $stats[self::ODPS_SIZE] - (integer)$options[self::ODPS_SEEK_TO];
                    }
                }
                $request->set_read_stream($options[self::ODPS_FILE_UPLOAD], $length);
            } else {
                $request->set_read_file($options[self::ODPS_FILE_UPLOAD]);
                $length = $request->read_stream_size;
                if (isset($options[self::ODPS_CONTENT_LENGTH])) {
                    $length = $options[self::ODPS_CONTENT_LENGTH];
                } elseif (isset($options[self::ODPS_SEEK_TO]) && isset($length)) {
                    $length -= (integer)$options[self::ODPS_SEEK_TO];
                }
                $request->set_read_stream_size($length);
            }
        }

        if (isset($options[self::ODPS_SEEK_TO])) {
            $request->set_seek_position((integer)$options[self::ODPS_SEEK_TO]);
        }

        if (isset($options[self::ODPS_FILE_DOWNLOAD])) {
            if (is_resource($options[self::ODPS_FILE_DOWNLOAD])) {
                $request->set_write_stream($options[self::ODPS_FILE_DOWNLOAD]);
            } else {
                $request->set_write_file($options[self::ODPS_FILE_DOWNLOAD]);
            }
        }

        $string_to_sign = '';
        $headers = $this->generateHeaders($options);

        if (isset($options[self::ODPS_METHOD])) {
            $request->set_method($options[self::ODPS_METHOD]);
            $string_to_sign .= $options[self::ODPS_METHOD] . "\n";
        }

        if (isset($options[self::ODPS_CONTENT])) {
            $request->set_body($options[self::ODPS_CONTENT]);
            if ($headers[self::ODPS_CONTENT_TYPE] === 'application/x-www-form-urlencoded') {
                $headers[self::ODPS_CONTENT_TYPE] = 'application/octet-stream';
            }

            if (isset($headers[self::TransferEncoding])) {

            } else {
                $headers[self::ODPS_CONTENT_LENGTH] = strlen($options[self::ODPS_CONTENT]);
                $headers[self::ODPS_CONTENT_MD5] = md5($options[self::ODPS_CONTENT]);
            }

        }

        uksort($headers, 'strnatcasecmp');

        foreach ($headers as $header_key => $header_value) {
            $header_value = str_replace(array("\r", "\n"), '', $header_value);
            if ($header_value !== '') {
                $request->add_header($header_key, $header_value);
            }
            if (strtolower($header_key) === 'content-md5'
                || strtolower($header_key) === 'content-type'
                || strtolower($header_key) === 'date'
            ) {
                $string_to_sign .= $header_value . "\n";
            } elseif (strpos(strtolower($header_key), self::ODPS_DEFAULT_PREFIX) === 0) {
                $string_to_sign .= strtolower($header_key) . ':' . $header_value . "\n";
            }
        }
        $request->add_header("Expect", "");

        $string_to_sign .= rawurldecode($signable_resource) . urldecode($signable_query_string);

        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->accessKeySecret, true));

        $request->add_header('Authorization', 'ODPS ' . $this->accessKeyId . ':' . $signature);

        if ($this->timeout !== 0) {
            $request->timeout = $this->timeout;
        }
        if ($this->connectTimeout !== 0) {
            $request->connect_timeout = $this->connectTimeout;
        }

        $data = null;
        try {

            if (isset($options["JustOpenSocket"])) {
                return $request->openRequestSocket();
            } else {
                $data = $request->send_request();
            }
        } catch (RequestCore_Exception $e) {
            throw(new OdpsException('RequestCoreException: ' . $e->getMessage()));
        }
        $response_header = $request->get_response_header();
        $response_header['odps-request-url'] = $this->requestUrl;
        $response_header['odps-redirects'] = $this->redirects;
        $response_header['odps-stringtosign'] = $string_to_sign;
        $response_header['odps-requestheaders'] = $request->request_headers;

        //  $data = new ResponseCore($response_header, $request->get_response_body(), $request->get_response_code());
        //retry if ODPS Internal Error
        if ((integer)$request->get_response_code() === 500) {
            if ($this->redirects <= $this->maxRetries) {

                $delay = (integer)(pow(4, $this->redirects) * 100000);
                usleep($delay);
                $this->redirects++;
                $data = $this->call($options);
            }
        }

        if (!$this->debug && (integer)$request->get_response_code() >= 400) {
            throw(new OdpsException($data->body));
        }

        $this->redirects = 0;

        if ($this->debug) {
            $urlCompontents = parse_url($request->request_url);

            $data->debugInfo = array(
                "requestMethod" => $request->method,
                "requestUrl" => $request->request_url,
                "requestPath" => $urlCompontents["path"],
                "requestBody" => $request->request_body
            );;
        }

        return $data;
    }

    /**
     * @param $endpoint
     * @return mixed
     * @throws OdpsException
     */
    private function verifyEndpoint($endpoint)
    {
        if (strpos($endpoint, 'http://') === 0 || strpos($endpoint, 'https://') === 0) {
            return $endpoint;
        }

        throw new OdpsException("Invalid endpoint");
    }

    private function _makeSureCurlEnabled()
    {
        if (function_exists('get_loaded_extensions')) {

            $enabled_extension = array("curl");
            $extensions = get_loaded_extensions();
            if ($extensions) {
                foreach ($enabled_extension as $item) {
                    if (!in_array($item, $extensions)) {
                        throw new OdpsException("Extension {" . $item . "} is not installed or not enabled, please check your php env.");
                    }
                }
            } else {
                throw new OdpsException("function get_loaded_extensions not found.");
            }
        } else {
            throw new OdpsException('Function get_loaded_extensions has been disabled, please check php config.');
        }
    }

    /**
     *  Generate to be signed strng
     *
     * @param  mixed $options
     * @return string
     */
    private function generateSignableResource($options)
    {
        $signableResource = "";
        $signableResource .= '/';

        //signable_resource + object
        if (isset($options[self::ODPS_RESOURCE]) && '/' !== $options[self::ODPS_RESOURCE]) {
            $signableResource = str_replace(array('%2F', '%25'), array('/', '%'), rawurlencode($options[self::ODPS_RESOURCE]));
        }

        return $signableResource;
    }

    /**
     * Generate to be signed querystring
     *
     * @param  array $options
     * @return array
     */
    private function generateSignableQueryStringParam($options)
    {
        if (array_key_exists(self::ODPS_PARAMS, $options)) {
            $params = array();
            foreach ($options[self::ODPS_PARAMS] as $k => $v) {
                if ($v !== null && $v !== "") {
                    $params[$k] = $v;
                }
            }

            return $params;
        } else {
            return array();
        }
    }

    /**
     * Generate full query_string
     *
     * @param  mixed $options
     * @return string
     */
    private function generateQueryString($options)
    {
        $queryStringParams = array();
        if (isset($options[self::ODPS_QUERY_STRING])) {
            $queryStringParams = array_merge($queryStringParams, $options[self::ODPS_QUERY_STRING]);
        }
        return OdpsUtil::toQueryString($queryStringParams, null);
    }

    private function _getAccessUrl($options = array())
    {
        if (isset($options[static::ODPS_ENDPOINT])) {
            return $options[static::ODPS_ENDPOINT];
        }
        return $this->endPoint;
    }

    private function generateUserAgent()
    {
        return self::ODPS_NAME . "/" . self::ODPS_VERSION . " (" . php_uname('s') . "/" . php_uname('r') . "/" . php_uname('m') . ";" . PHP_VERSION . ")";
    }

    /**
     * Generate headers
     *
     * @param  mixed $options
     * @param  string $hostname hostname
     * @return array
     */
    private function generateHeaders($options)
    {
        $headers = array(
            self::ODPS_CONTENT_MD5 => '',
            self::ODPS_CONTENT_TYPE => isset($options[self::ODPS_CONTENT_TYPE]) ? $options[self::ODPS_CONTENT_TYPE] : self::DEFAULT_CONTENT_TYPE,
            self::ODPS_DATE => isset($options[self::ODPS_DATE]) ? $options[self::ODPS_DATE] : gmdate('D, d M Y H:i:s \G\M\T')
        );

        if (isset($options[self::ODPS_CONTENT_MD5])) {
            $headers[self::ODPS_CONTENT_MD5] = $options[self::ODPS_CONTENT_MD5];
        }

        //�ϲ�HTTP headers
        if (isset($options[self::ODPS_HEADERS])) {
            $headers = array_merge($headers, $options[self::ODPS_HEADERS]);
        }

        if (isset($options[self::ODPS_USER_AGENT])) {
            $headers[self::ODPS_USER_AGENT] = $options[self::ODPS_USER_AGENT];
        } else {
            $headers[self::ODPS_USER_AGENT] = $this->generateUserAgent();
        }

        return $headers;
    }
}
