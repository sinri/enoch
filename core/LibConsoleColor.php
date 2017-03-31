<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/3/30
 * Time: 16:51
 */

namespace sinri\enoch\core;


class LibConsoleColor
{
    const Black = "0;30";
    const DarkGray = "1;30";
    const Blue = "0;34";
    const LightBlue = "1;34";
    const Green = "0;32";
    const LightGreen = "1;32";
    const Cyan = "0;36";
    const LightCyan = "1;36";
    const Red = "0;31";
    const LightRed = "1;31";
    const Purple = "0;35";
    const LightPurple = "1;35";
    const Brown = "0;33";
    const Yellow = "1;33";
    const LightGray = "0;37";
    const White = "1;37";

    public function __construct()
    {
        // do nothing
    }

    public function getColorWord($text, $color_code)
    {
        $color_text = $this->getColorStart($color_code) . $text . $this->getColorEnd();
        return $color_text;
    }

    protected function getColorStart($color_code)
    {
        if (!preg_match('/^\d\d?;\d\d?$/', $color_code)) {
            throw new \Exception("Not a standard color code.");
        }
        return "\033[" . $color_code . "m";
    }

    protected function getColorEnd()
    {
        return "\033[0m";
    }

    private static $instance = null;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new LibConsoleColor();
        }
        return self::$instance;
    }

    public static function output($text, $color_code)
    {
        echo self::getInstance()->getColorWord($text, $color_code);
    }
}