<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/9
 * Time: 23:41
 */

namespace sinri\enoch\core;


use sinri\enoch\helper\CommonHelper;

class LibRequest
{
    const METHOD_HEAD = "HEAD";//since v1.3.0
    const METHOD_GET = "GET";//since v1.3.0
    const METHOD_POST = "POST";//since v1.3.0
    const METHOD_PUT = "PUT";//since v1.3.0
    const METHOD_DELETE = "DELETE";//since v1.3.0
    const METHOD_OPTION = "OPTION";//since v1.3.0
    const METHOD_PATCH = "PATCH";//since v1.3.0
    const METHOD_CLI = "cli";//since v1.3.0

    protected $helper;

    const IP_TYPE_V4 = "IPv4";
    const IP_TYPE_V6 = "IPv6";

    protected $ip_address;

    public function __construct()
    {
        $this->helper = new CommonHelper();
        $this->ip_address = false;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function getRequest($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_REQUEST, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function get($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_GET, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function post($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_POST, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @since 1.3.8
     * @return bool|string
     */
    public function getRequestContent()
    {
        return file_get_contents('php://input');
    }

    /**
     * @since 1.3.8
     * @param bool $assoc
     * @return mixed
     */
    public function getRequestContentAsJson($assoc = true)
    {
        $text = $this->getRequestContent();
        return @json_decode($text, $assoc);
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function getCookie($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_COOKIE, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function getServerVar($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_SERVER, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function getSessionVar($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($_SESSION, $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $regex
     * @param int $error
     * @return mixed
     */
    public function getHeader($name, $default = null, $regex = null, &$error = 0)
    {
        $value = $this->helper->safeReadArray($this->fullHeaderFields(), $name, $default, $regex, $error);
        return $value;
    }

    /**
     * @return array
     */
    public function fullPostFields()
    {
        return $_POST ? $_POST : [];
    }

    /**
     * @return array
     */
    public function fullGetFields()
    {
        return $_GET ? $_GET : [];
    }

    /**
     * @return array
     */
    public function fullCookieFields()
    {
        return $_COOKIE ? $_COOKIE : [];
    }

    /**
     * @return array|false|string
     */
    public function fullHeaderFields()
    {
        return getallheaders();
    }

    /**
     * @param null $wechatVersion
     * @return bool
     */
    public function hasWeChatUserAgent(&$wechatVersion = null)
    {
        $isFromWechat = false;
        $user_agent = $this->getHeader('User-Agent', '');
        if (stripos($user_agent, 'MicroMessenger') !== false) {
            $isFromWechat = true;
        }
        preg_match('/MicroMessenger\/([\d\.]+)/', $user_agent, $match);
        if (is_array($match) && isset($match[1])) {
            $wechatVersion = $match[1];
        }
        return $isFromWechat;
    }

    /**
     * 是否是AJAx提交的
     * @return bool
     */
    public function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            return true;
        }
        return false;
    }

    /**
     * 是否是GET提交的
     * @return bool
     */
    public function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    /**
     * 是否是POST提交
     * @return bool
     */
    public function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
    }

    /**
     * @since v1.1.0
     * @return string|bool return request method, or false on failed.
     */
    public function getRequestMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return $_SERVER['REQUEST_METHOD'];
        }
        return $this->isCLI() ? self::METHOD_CLI : false;
    }

    /**
     * @return bool
     */
    public function isCLI()
    {
        return (php_sapi_name() === 'cli') ? true : false;
    }

    /**
     * Fetch the IP Address
     *
     * Determines and validates the visitor's IP address.
     *
     * @since 1.3.9 @see CodeIgniter Core
     * @param array $proxy_ips
     * @return string IP address
     */
    public function ip_address($proxy_ips = [])
    {
        if ($this->ip_address) {
            return $this->ip_address;
        }
        $this->ip_address = $this->getServerVar('REMOTE_ADDR');

        if ($proxy_ips) {
            $spoof = false;
            foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header) {
                if (($spoof = $this->getServerVar($header)) !== NULL) {
                    // Some proxies typically list the whole chain of IP
                    // addresses through which the client has reached us.
                    // e.g. client_ip, proxy_ip1, proxy_ip2, etc.
                    sscanf($spoof, '%[^,]', $spoof);

                    if (!$this->valid_ip($spoof)) {
                        $spoof = NULL;
                    } else {
                        break;
                    }
                }
            }

            if ($spoof) {
                for ($i = 0, $c = count($proxy_ips); $i < $c; $i++) {
                    // Check if we have an IP address or a subnet
                    if (strpos($proxy_ips[$i], '/') === FALSE) {
                        // An IP address (and not a subnet) is specified.
                        // We can compare right away.
                        if ($proxy_ips[$i] === $this->ip_address) {
                            $this->ip_address = $spoof;
                            break;
                        }

                        continue;
                    }

                    // We have a subnet ... now the heavy lifting begins
                    isset($separator) OR $separator = $this->valid_ip($this->ip_address, self::IP_TYPE_V6) ? ':' : '.';

                    // If the proxy entry doesn't match the IP protocol - skip it
                    if (strpos($proxy_ips[$i], $separator) === FALSE) {
                        continue;
                    }

                    // Convert the REMOTE_ADDR IP address to binary, if needed
                    if (!isset($ip, $sprintf)) {
                        if ($separator === ':') {
                            // Make sure we're have the "full" IPv6 format
                            $ip = explode(':',
                                str_replace('::',
                                    str_repeat(':', 9 - substr_count($this->ip_address, ':')),
                                    $this->ip_address
                                )
                            );

                            for ($j = 0; $j < 8; $j++) {
                                $ip[$j] = intval($ip[$j], 16);
                            }

                            $sprintf = '%016b%016b%016b%016b%016b%016b%016b%016b';
                        } else {
                            $ip = explode('.', $this->ip_address);
                            $sprintf = '%08b%08b%08b%08b';
                        }

                        $ip = vsprintf($sprintf, $ip);
                    }

                    // Split the netmask length off the network address
                    $netaddr = null;
                    $masklen = null;
                    sscanf($proxy_ips[$i], '%[^/]/%d', $netaddr, $masklen);

                    // Again, an IPv6 address is most likely in a compressed form
                    if ($separator === ':') {
                        $netaddr = explode(
                            ':',
                            str_replace(
                                '::',
                                str_repeat(':', 9 - substr_count($netaddr, ':')),
                                $netaddr
                            )
                        );
                        for ($i = 0; $i < 8; $i++) {
                            $netaddr[$i] = intval($netaddr[$i], 16);
                        }
                    } else {
                        $netaddr = explode('.', $netaddr);
                    }

                    // Convert to binary and finally compare
                    if (strncmp($ip, vsprintf($sprintf, $netaddr), $masklen) === 0) {
                        $this->ip_address = $spoof;
                        break;
                    }
                }
            }
        }

        if (!$this->valid_ip($this->ip_address)) {
            return $this->ip_address = '0.0.0.0';
        }

        return $this->ip_address;
    }

    /**
     * Validate IP Address
     *
     * @since 1.3.9 @see CodeIgniter Core
     * @param    string $ip IP address
     * @param    string $which IP protocol: 'ipv4' or 'ipv6'
     * @return    bool
     */
    public function valid_ip($ip, $which = '')
    {
        switch (strtolower($which)) {
            case self::IP_TYPE_V4:
                $which = FILTER_FLAG_IPV4;
                break;
            case self::IP_TYPE_V6:
                $which = FILTER_FLAG_IPV6;
                break;
            default:
                $which = NULL;
                break;
        }

        return (bool)filter_var($ip, FILTER_VALIDATE_IP, $which);
    }


    /**
     * @since 1.3.9
     * @param ip
     * @return string IP_TYPE_V?
     */
    public function ipVersion($ip)
    {
        $v = strpos($ip, ":") === false ? self::IP_TYPE_V4 : self::IP_TYPE_V6;
        return $v;
    }

    // upload process

    /**
     * @param string $name
     * @param callable $callback
     * @param string $error
     * @return bool|mixed
     */
    public function handleUploadFileWithCallback($name, $callback, &$error = '')
    {
        if (!isset($_FILES[$name])) {
            return false;
        }
        $error = $_FILES[$name]['error'];
        if ($error !== UPLOAD_ERR_OK) {
            return false;
        }
        $original_file_name = $_FILES[$name]['name'];
        $file_type = $_FILES[$name]['type'];
        $file_size = $_FILES[$name]['size'];
        $file_tmp_name = $_FILES[$name]['tmp_name'];

        // where you might need `move_uploaded_file`
        return call_user_func_array($callback, [$original_file_name, $file_type, $file_size, $file_tmp_name, $error]);
    }

    /**
     * @param $name
     * @param $callback
     * @param string $error
     * @return bool
     */
    public function handleUploadFilesWithCallback($name, $callback, &$error = '')
    {
        if (!isset($_FILES[$name]) && !is_array($_FILES[$name])) {
            return false;
        }
        $error = [];
        foreach ($_FILES[$name] as $index => $item) {
            $error[$index] = $item['error'];
            if ($error[$index] !== UPLOAD_ERR_OK) {
                return false;
            }
        }
        foreach ($_FILES[$name] as $index => $item) {
            $original_file_name = $item['name'];
            $file_type = $item['type'];
            $file_size = $item['size'];
            $file_tmp_name = $item['tmp_name'];

            // where you might need `move_uploaded_file`
            $done = call_user_func_array($callback, [$original_file_name, $file_type, $file_size, $file_tmp_name, $error]);
            if (!$done) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $code
     * @return string
     */
    public function descUploadFileError($code)
    {
        $phpFileUploadErrors = array(
            UPLOAD_ERR_OK => 'There is no error, the file uploaded with success',
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
        );
        return $this->helper->safeReadArray($phpFileUploadErrors, $code, 'Non System Error');
    }
}