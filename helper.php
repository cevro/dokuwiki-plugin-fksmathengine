<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michal Červeňák <miso@fykos.cz>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

if (!defined('DOKU_LF')) {
    define('DOKU_LF', "\n");
}
if (!defined('DOKU_TAB')) {
    define('DOKU_TAB', "\t");
}

class helper_plugin_fksmathengine extends DokuWiki_Plugin {

    function parsedata() {
        global $data;
        $data = preg_split('/;/', $data);
        foreach ($data as $key) {
            $splitdata = preg_split('/:/', $key);
            $data[$splitdata[0]] = $splitdata[1];
        }
        $data['vars'] = preg_split('/,/', $data['var']);
        $data['consts'] = preg_split('/,/', $data['const']);
    }

    function addvisible() {
        global $data;
        global $to_page;
        foreach ($data['vars'] as $key) {
            $key = preg_split('/=/', $key);
            $key[0] = str_replace(' ', '', $key[0]);
            $label = $this->getlabel($key[1]);
            $to_page.='<p>' . $this->addlabel($label[0]) . '<input type="text" id="' . $key[0] . '" name="' . $key[0] . '" value=" " class="edit" />' . $this->addJd($label[1]) . '</p>';
        }
    }

    function addhidden() {
        global $data;
        global $to_page;
        foreach ($data['consts'] as $key) {
            $key = preg_split('/=/', $key);
            $to_page.='<input type="hidden" id="' . $key[0] . '" name="' . $key[0] . '" value="' . $key[1] . '" />';
        }
    }

    function addoutput() {
        global $data;
        global $to_page;
        $math = preg_split('/=/', $data['math']);
        $to_page.='<script>
                function engine(){
                document.getElementById("results").value=' . $this->getscript($math[2]) . ';
                };
                </script>';

        $label = $this->getlabel($math[1]);
        $to_page.='<input type="submit" onclick="engine()" value="'.$this->getLang('calculate').'" />';

        $to_page.='<p>' . $this->addlabel($label[0]) . '<input id="results" readonly="redonly" class="edit">' . $this->addJd($label[1]) . '</p>';
    }

    function getlabel($text) {
        return preg_split('/\|/', $text);
    }

    function addlabel($lab) {
        return '<label>' . $lab . ': </label>';
    }

    function addJd($lab) {
        return '<label>$' . $lab . '$</label>';
    }

    function getscript($scr) {
        global $data;
        $scr = str_replace('\\', 'Math.', $scr);
        $scr = str_replace('{', 'document.getElementById("', $scr);
        $scr = str_replace('}', '").value', $scr);
        return $scr;
    }

}
