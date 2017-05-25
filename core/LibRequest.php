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
                        $netaddr = explode(':', str_replace('::', str_repeat(':', 9 - substr_count($netaddr, ':')), $netaddr));
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
}