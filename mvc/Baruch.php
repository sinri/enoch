<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/5/28
 * Time: 22:26
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\LibRequest;
use sinri\enoch\core\LibResponse;

/**
 * Class Baruch
 * This is a simple wiki framework
 * @since 1.4.4
 * @package sinri\enoch\mvc
 */
class Baruch
{
    protected $gateway;
    protected $storage;
    protected $request;
    protected $response;
    protected $extension;
    protected $homepage;

    public function __construct()
    {
        $this->gateway = "index.php";
        $this->storage = __DIR__ . '/storage';
        $this->request = new LibRequest();
        $this->response = new LibResponse();
        $this->extension = "";
        $this->homepage = "index";
    }

    /**
     * @param string $homepage
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * Default as "", you can set as ".md" or ".txt", etc.
     * @param string $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @param string $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param string $gateway
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }

    public function handleWiki()
    {
        $components = $this->getPathComponents();
        $file = $this->seekTargetFile($components);

        $act = $this->request->getRequest("act", "read");
        $data = $this->request->getRequest("data", "");
        if ($act === "read") {
            $this->actRead($file);
        } elseif ($act === 'write') {
            $this->actWrite($file, $data);
        } else {
            throw new BaseCodedException("no such act type");
        }
    }

    protected function getPathComponents()
    {
        $string = $this->getPathString();
        $string = preg_replace('/\?.*/', '', $string);
        $components = explode('/', $string);
        $components = array_filter($components, function ($var) {
            return !empty($var) && $var != '.' && $var != '..';
        });
        array_walk($components, function (&$item) {
            $item = urldecode($item);
        });
        return $components;
    }

    protected function getPathString()
    {
        $prefix = $_SERVER['SCRIPT_NAME'];
        if (
            (strpos($_SERVER['REQUEST_URI'], $prefix) !== 0)
            && (strrpos($prefix, '/' . $this->gateway) + 10 == strlen($prefix))
        ) {
            $prefix = substr($prefix, 0, strlen($prefix) - 10);
        }

        return substr($_SERVER['REQUEST_URI'], strlen($prefix));
    }

    protected function seekTargetFile($components = [])
    {
        if (empty($components)) {
            $components = [$this->homepage];
        }
        $file = implode("/", $components);
        $dir = $this->storage . '/' . $file;
        $file = $this->storage . '/' . $file . $this->extension;
        if (file_exists($file) && is_dir($file)) {
            $file .= "/" . $this->homepage . $this->extension;
        } elseif (!file_exists($file) && file_exists($dir) && is_dir($dir)) {
            $file = $dir . "/" . $this->homepage . $this->extension;
        }
        return $file;
    }

    protected function actRead($file)
    {
        if (!file_exists($file)) {
            $this->pageNotFound($file);
            return false;
        }
        $content = file_get_contents($file);
        echo $content;
        return true;
    }

    protected function pageNotFound($file)
    {
        echo "File not found: {$file}";
    }

    protected function actWrite($file, $data)
    {
        if (!file_exists($file)) {
            if (!preg_match('/^(.+)\/[^\/]*\.md$/', $file, $matches)) {
                throw new BaseCodedException('no dir found');
            }
            if (isset($matches[1])) {
                @mkdir($matches[1], 0777, true);
            }
        }
        $written = file_put_contents($file, $data);
        if ($written) {
            $this->response->jsonForAjax(LibResponse::AJAX_JSON_CODE_OK, ['written' => $written]);
        } else {
            $this->response->jsonForAjax(LibResponse::AJAX_JSON_CODE_FAIL, "failed");
        }
    }
}