<?php

/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michal Červeňák <miso@fykos.cz>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

if (!defined('DOKU_PLUGIN')) {
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
}

require_once(DOKU_PLUGIN . 'admin.php');

class admin_plugin_fksmathengine extends DokuWiki_Admin_Plugin {

    public function __construct() {
        
    }

    public function getMenuSort() {
        return 229;
    }

    public function forAdminOnly() {
        return false;
    }

    public function getMenuText($language) {
        $menutext = 'FKS_math_engine';
        return $menutext;
    }

    public function handle() {
        
    }

    public function html() {
        global $INPUT;


        $log = io_readFile(metaFN('fksmathengine:log_1', '.log'));
        $parse_log = explode("\n", $log);

        foreach ($parse_log as $value) {
            if (!empty($value)) {
                $log_lines[] = explode(';', $value);
            }
        }
        $sort = $INPUT->str('sort');

        if (!empty($sort)) {
            $id = (int) $sort;
            usort($log_lines, function($a, $b) use ($id) {
                return ($a[$id] < $b[$id]) ? -1 : 1;
            });
        }

        $NO = count($log_lines[0]);

        $form = new Doku_Form(array(), '?do=admin&page=fksmathengine', 'POST');
        $form->addHidden('do', 'admin');
        $form->addHidden('page', 'fksmathengine');
        $val = array('Sort by');
        for ($i = 0; $i < $NO; $i++) {
            $val[] = $i;
        }
        $form->addElement(form_makeMenuField('sort', $val, null,''));
        $form->addElement(form_makeButton('submit', null, 'Sort by'));
        echo '<div class="FKS_mathengine_sort">';
        html_form('nic', $form);
        echo'</div>';
        echo html_open_tag('table', array('class'=>'table table-striped FKS_mathengine_table'));
        
        echo '<thead><tr>';
        for ($i = 0; $i < $NO - 2; $i++) {
            echo '<th>Data</th>';
        }
        echo '<th>Time</th><th>IP</th></tr></thead>';

        foreach ($log_lines as $value) {
            echo '<tr><td>' . implode('</td><td>', $value) . '</td></tr>';
        }

        echo '</table>';
    }

}
