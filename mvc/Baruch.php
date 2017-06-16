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

    /**
     * Baruch constructor.
     */
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

    /**
     * @throws BaseCodedException
     */
    public function handleWiki()
    {
        $components = $this->getPathComponents();
        $file = $this->seekTargetFile($components, $tail);

        $act = $this->request->getRequest("act", "read");
        $data = $this->request->getRequest("data", "");
        if ($act === "read") {
            if ('' !== $tail) {
                $handled = $this->specialForAutoCompletedPath($components, $tail);
                if ($handled) {
                    return;
                }
            }
            $this->actRead($file);
        } elseif ($act === 'write') {
            $this->actWrite($file, $data);
        } elseif ($act === 'index') {
            $this->actIndex();
        } else {
            throw new BaseCodedException("no such act type");
        }
    }

    /**
     * @param $components
     * @param $tail
     * @return bool false for continue, true for handled
     */
    protected function specialForAutoCompletedPath($components, $tail)
    {
        $url = $_SERVER['REQUEST_URI'];
        if (substr($url, -1, 1) === '/') {
            $url = substr($url, 0, strlen($url) - 1);
        }
        header("Location: " . $url . $tail);
        return true;
    }

    /**
     * @return array
     */
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

    /**
     * @return bool|string
     */
    protected function getURLRootString()
    {
        $prefix = $_SERVER['SCRIPT_NAME'];
        if (
            (strpos($_SERVER['REQUEST_URI'], $prefix) !== 0)
            && (strrpos($prefix, '/' . $this->gateway) + 10 == strlen($prefix))
        ) {
            $prefix = substr($prefix, 0, strlen($prefix) - 10);
        }
        return $prefix;
    }

    /**
     * @return bool|string
     */
    protected function getPathString()
    {
        $prefix = $this->getURLRootString();
        return substr($_SERVER['REQUEST_URI'], strlen($prefix));
    }

    /**
     * @param array $components
     * @param string $tail
     * @return string
     */
    protected function seekTargetFile($components = [], &$tail = '')
    {
        if (empty($components)) {
            $components = [];//[$this->homepage];
        }
        $dir = $this->storage . '/' . implode("/", $components);// dir: a/b
        $file = $dir . $this->extension;// file: a/b.md
        $tail = "";
        if (file_exists($file)) {
            $tail = '';
        } elseif (
            file_exists($dir) && is_dir($dir)
            && file_exists($dir . '/' . $this->homepage . $this->extension)
        ) {
            $tail = "/" . $this->homepage;
            $file = $dir . $tail . $this->extension;
        }

        return $file;
    }

    /**
     * @param $file
     * @return bool
     */
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

    /**
     * @param $file
     */
    protected function pageNotFound($file)
    {
        echo "File not found: {$file}";
    }

    /**
     * @param $file
     * @param $data
     * @throws BaseCodedException
     */
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

    /**
     * Auto index
     */
    protected function actIndex()
    {
        $tree = $this->getIndexTree();
        echo json_encode($tree, JSON_PRETTY_PRINT);
    }

    /**
     * @param string $relative_path
     * @return array
     */
    protected function getIndexTree($relative_path = "")
    {
        $root_dir = $this->storage . '/' . $relative_path;
        $tree = [];
        if ($handle = opendir($root_dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
                if (is_dir($root_dir . '/' . $entry)) {
                    $temp = $this->getIndexTree($relative_path . '/' . $entry);
                    if (!empty($temp)) {
                        $tree[$entry] = $temp;
                    }
                } else {
                    $tree[$entry] = $relative_path . '/' . $entry;
                }
            }

            closedir($handle);
        }
        return $tree;
    }


}