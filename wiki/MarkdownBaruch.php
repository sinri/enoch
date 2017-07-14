<?php
/**
 * Created by PhpStorm.
 * User: Sinri
 * Date: 2017/6/16
 * Time: 17:05
 */

namespace sinri\enoch\wiki;


//use cebe\markdown\GithubMarkdown;
use sinri\enoch\mvc\Baruch;

class MarkdownBaruch extends Baruch
{
    protected $parser;

    /**
     * @return mixed
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Set the Markdown Code Parse, Such as `cebe\markdown\GithubMarkdown`
     * @param $parser
     */
    public function setParser($parser)
    {
        $this->parser = $parser;
    }

    protected $wikiTitle;
    protected $wikiHost;
    protected $wikiHostLink;

    /**
     * MarkdownBaruch constructor.
     * @param string $wikiTitle
     * @param string $wikiHost
     * @param string $wikiHostLink
     */
    public function __construct($wikiTitle = "Baruch Wiki", $wikiHost = "Public Domain", $wikiHostLink = "#")
    {
        parent::__construct();
        //$this->parser = new GithubMarkdown();
        //$this->parser->html5 = true;
        //$this->configReader = new ConfigReader();

        $this->wikiTitle = $wikiTitle;
        $this->wikiHost = $wikiHost;
        $this->wikiHostLink = $wikiHostLink;
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
        //$content = $this->parser->parse($content);
        $content = call_user_func_array([$this->parser, 'parse'], [$content]);

        $components = $this->getPathComponents();
        array_unshift($components, "Root");

        $this->buildHTML($content, $components);

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
     */
    protected function actWrite($file, $data)
    {
        echo "...";
    }

    /**
     * Auto Index
     */
    protected function actIndex()
    {
        $tree = $this->getIndexTree();

        $content = "<h1>Index</h1>";
        $content .= $this->processIndexTreeToHTML($tree);

        $this->buildHTML($content, ["Index"]);
    }

    /**
     * @param $tree
     * @return string
     */
    private function processIndexTreeToHTML($tree)
    {
        $html = "<ul class='index-tree-div'>";
        foreach ($tree as $key => $sub_tree) {
            if (!is_array($sub_tree)) {
                $html .= "<li><a href='.{$sub_tree}'>{$key}</a></li>";
                continue;
            }
            $sub_html = $this->processIndexTreeToHTML($sub_tree);
            $html .= "<li>
                <p class='index-tree-div'>{$key}</p>
                <ul class='index-tree-div'>{$sub_html}</ul>
            </li>";
        }
        $html .= "</ul>";
        return $html;
    }

    /**
     * @param $content
     * @param $components
     */
    protected function buildHTML($content, $components)
    {
        $autoURLBase = $this->getURLRootString();
        $title = $this->wikiTitle;
        $navigator = "";
        if (count($components) > 0) {
            $title = $components[count($components) - 1] . " - " . $this->wikiTitle;
            //$navigator=implode(" / ",$components);
            $navigator = [];
            $index = 0;
            $url = $autoURLBase . "/";
            foreach ($components as $component) {
                if ($index === 0) {
                    // do nothing
                } else if ($index === count($components) - 1) {
                    $url .= $component;
                } else {
                    $url .= $component . "/";
                }
                $index++;
                $navigator[] = "<a href='{$url}'>{$component}</a>";
            }
            $navigator = implode(" / ", $navigator);
        }

        echo "<!doctype html>";
        echo "<html>";
        echo "<head>";
        echo "<meta charset='utf-8'/>";
        echo "<title>{$title}</title>";
        echo "<style>
                body {
                    color: #333;
                    background: #fbfaf9 url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAABzCAYAAABZ/2hDAAAAmElEQ…2Uz/u2nypkdRT9Enn+Cgkl/UrlsYNB+/gGAhJin89c/adf1hUppirdLEYAAAAASUVORK5CYII=') top left repeat-x;
                }
        
                #board {
                    width: 90%;
                    margin: auto;
                }
        
                #banner {
                    font-family: serif;
                    font-size: 20px;
                }
        
                #navigator {
                    margin-top: 10px;
                    border-top: 1px solid gray;
                    border-bottom: 1px solid gray;
                    padding: 5px;
                    font-family: monospace;
                }
        
                #reader {
                    border: 1px solid #eee;
                    box-shadow: 0 0 0.5em #999;
                    border-radius: 2px;
                    margin-top: 20px;
                    padding: 1em;
                    word-wrap: break-word;
                    background-color: white;
                    min-height: 500px;
                }
        
                #footer {
                    margin-top: 20px;
                    border-top: 1px solid #eeeeee;
                    font-family: serif;
                    font-size: 15px;
                    text-align: center;
                }
        
                code {
                    color: #ff5050;
                    background-color: rgba(211, 211, 211, 0.5);
                    padding: 2px;
                    border-radius: 2px;
                    font-family: \"Lucida Console\", monospace;
                    font-size: 13px;
                    line-height: 16px;
                }
        
                blockquote {
                    background: #f9f9f9;
                    border-left: 10px solid #ccc;
                    margin: 1.5em 10px;
                    padding: 0.5em 10px;
                    quotes: \"\\201C\" \"\\201D\" \"\\2018\" \"\\2019\";
                }
        
                blockquote:before {
                    color: #ccc;
                    content: open-quote;
                    font-size: 2em;
                    line-height: 0.1em;
                    margin-right: 0.25em;
                    vertical-align: -0.4em;
                }
        
                blockquote:after {
                    color: #ccc;
                    content: close-quote;
                    font-size: 2em;
                    line-height: 0.1em;
                    margin-right: 0.25em;
                    vertical-align: -0.4em;
                }
        
                blockquote p {
                    display: inline;
                }
        
                ul.index-tree-div {
                    margin: 0 10px;
                    padding: 0;
                }
        
                p.index-tree-div {
                    margin: 5px 0;
                }
            </style>";
        echo "</head>";
        echo "<body>
                <div id='board'>
                    <div id='banner'>{$this->wikiTitle}</div>
                    <div id='navigator'>{$navigator}</div>
                    <div id='reader'>{$content}</div>
                    <div id='footer'>
                        <a href='{$autoURLBase}'>Wiki Homepage</a> 
                        |
                        <a href='{$this->wikiHostLink}'>{$this->wikiHost}</a>
                        |
                        Powered by
                        <a href=\"https://github.com/sinri/enoch\">sinri/enoch</a>
                        and
                        <a href=\"http://markdown.cebe.cc/\">cebe/markdown</a>.
                    </div>
                </div>
            </body>";
        echo "</html>";
    }
}