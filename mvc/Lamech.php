<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/4/16
 * Time: 22:42
 */

namespace sinri\enoch\mvc;


use sinri\enoch\core\LibSession;
use sinri\enoch\core\Spirit;

class Lamech
{
    protected $session_dir;
    protected $controller_dir;
    protected $view_dir;
    protected $error_page;

    public function __construct($session_dir, $controller_dir, $view_dir, $error_page = null)
    {
        $this->session_dir = $session_dir;
        $this->controller_dir = $controller_dir;
        $this->view_dir = $view_dir;
        $this->error_page = $error_page;
    }

    /**
     * @return mixed
     */
    public function getSessionDir()
    {
        return $this->session_dir;
    }

    /**
     * @param mixed $session_dir
     */
    public function setSessionDir($session_dir)
    {
        $this->session_dir = $session_dir;
    }

    /**
     * @return mixed
     */
    public function getControllerDir()
    {
        return $this->controller_dir;
    }

    /**
     * @param mixed $controller_dir
     */
    public function setControllerDir($controller_dir)
    {
        $this->controller_dir = $controller_dir;
    }

    /**
     * @return mixed
     */
    public function getViewDir()
    {
        return $this->view_dir;
    }

    /**
     * @param mixed $view_dir
     */
    public function setViewDir($view_dir)
    {
        $this->view_dir = $view_dir;
    }

    public function startSession()
    {
        LibSession::sessionStart($this->session_dir);
    }

    public function viewFromRequest()
    {
        $spirit = Spirit::getInstance();
        $act = $spirit->getRequest("act", 'index', "/^[A-Za-z0-9_]+$/", $error);
        if ($error === Spirit::REQUEST_REGEX_NOT_MATCH) {
            $spirit->errorPage("Act input does not correct.", $this->error_page);
        } else {
            //act 种类
            try {
                $view_path = $this->view_dir . '/' . $act . ".php";
                if (!file_exists($view_path)) {
                    throw new BaseCodedException("Act missing", BaseCodedException::ACT_NOT_EXISTS);
                }
                $spirit->displayPage($view_path, []);
            } catch (\Exception $exception) {
                $spirit->errorPage("Act met error: " . $exception->getMessage(), $this->error_page);
            }
        }
    }

    public function apiFromRequest($api_namespace = "\\")
    {
        $spirit = Spirit::getInstance();
        $act = $spirit->getRequest("act", '', "/^[A-Za-z0-9_]+$/", $error);
        if ($error !== Spirit::REQUEST_NO_ERROR) {
            $spirit->jsonForAjax(Spirit::AJAX_JSON_CODE_FAIL, "不正常的请求不理：" . $error);
            return;
        }
        try {
            $target_class = $api_namespace . $act;
            $target_class_path = $this->controller_dir . '/' . $act . '.php';
            if (!file_exists($target_class_path)) {
                throw new BaseCodedException("模块已经死了");
            }
            require_once $target_class_path;
            $api = new $target_class();
            $api->work();
        } catch (BaseCodedException $exception) {
            $spirit->jsonForAjax(
                Spirit::AJAX_JSON_CODE_FAIL,
                ["error_code" => $exception->getCode(), "error_msg" => "请求处理异常：" . $exception->getMessage()]
            );
        }
    }
}