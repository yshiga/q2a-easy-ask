<?php

class qa_html_theme_layer extends qa_html_theme_base
{
    public function head_css()
    {
        qa_html_theme_base::head_css();
        if ($this->template==='easy-ask') {
            $styles =<<<EOS
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/angular-material/1.1.5/angular-material.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic">
EOS;
            $this->output($styles);
            $this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.QA_HTML_THEME_LAYER_URLTOROOT.'css/easy-ask.css"/>');
        }
    }

    public function body_footer()
    {
        $url = QA_HTML_THEME_LAYER_URLTOROOT;
        qa_html_theme_base::body_footer();
        if ($this->template === 'easy-ask') {
            $form_id = qa_request_part(1);
            $scripts =<<<EOS
    <!-- Angular Material requires Angular.js Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-animate.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-aria.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular-messages.min.js"></script>
    <script src="https://code.angularjs.org/1.5.5/i18n/angular-locale_ja-jp.js"></script>
    <!-- Angular Material Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-material/1.1.5/angular-material.min.js"></script>

    <script src="{$url}js/ng-file-upload-shim.min.js"></script>
    <script src="{$url}js/ng-file-upload.min.js"></script>
EOS;
            $this->output($scripts);
            $js_lang = $this->get_js_lang($form_id);
            $js_lang_json = json_encode( $js_lang );
            $jsvar = <<<EOS2
<script>
var easyask = window.easyask = window.easyask ? window.easyask : {};
easyask.code = '{$this->content["security_code"]}';
easyask.lang = {$js_lang_json};

var formatDate = function(date, format) {
    format = format.replace(/yyyy/g, date.getFullYear());
    format = format.replace(/M/g, (date.getMonth() + 1));
    format = format.replace(/d/g, (date.getDate()));
    return format;
};
</script>
EOS2;
            $this->output($jsvar);
            $this->output('<script src="'.$url.'js/easy-ask_'.$form_id.'.js"></script>');
        }
    }

    public function main_parts($content)
    {

        if($this->template === 'easy-ask') {
            if (!qa_is_logged_in()) {
                $this->no_login_message();
            } else {
                $userid = qa_get_logged_in_userid();
                $prev_question = $this->get_prev_question($userid);
                if (!empty($prev_question)) {
                    $this->output_prev_question($prev_question);
                    return;
                }
                $form_id = qa_request_part(1);
                $form_template = QEA_DIR.'/html/form_'.$form_id.'.html';
                if (file_exists($form_template)) {
                    $tmpl = file_get_contents($form_template);
                    $params = $this->get_params($form_id);
                    $html = strtr($tmpl, $params);
                    $this->output($html);
                } else {
                    $this->output( 'form not found!!!' );
                }
            }
        } else {
            qa_html_theme_base::main_parts($content);
        }
    }

    private function get_params($form_id)
    {
        $url = QA_HTML_THEME_LAYER_URLTOROOT;
        $file_max_size_mb = number_format(qa_opt('medium_editor_upload_max_size') / 1048576, 0) . 'MB';
        $common_params = array(
            '^url'                 => $url,
            '^page_title'          => $this->content['title'],
            '^comment_required'    => qa_lang('qea_lang/comment_required'),
            '^comment'             => qa_lang('qea_lang/comment'),
            '^required'            => qa_lang('qea_lang/required'),
            '^optional'            => qa_lang('qea_lang/optional'),
            '^yes'                 => qa_lang('qea_lang/yes'),
            '^no'                  => qa_lang('qea_lang/no'),
            '^post_complete'       => qa_lang('qea_lang/post_complete'),
            '^post_comp_msg'       => qa_lang('qea_lang/post_comp_msg'),
            '^check_button_label'  => qa_lang('qea_lang/check_button_label'),
            '^post'                => qa_lang('qea_lang/post'),
            '^add_image'           => qa_lang('qea_lang/add_image'),
            '^delete_image'        => qa_lang('qea_lang/delete_image'),
            '^file_max_size'       => $file_max_size_mb,
            '^file_max_size_error' => qa_lang_sub('qea_lang/file_max_size_error', $file_max_size_mb),
            '^file_type_error'     => qa_lang('qea_lang/file_type_error'),
            '^required_select'     => qa_lang('qea_lang/q1_required_select'),
            '^error_required'      => qa_lang('qea_lang/required_msg'),
            '^error_minlength'     => qa_lang_sub('qea_lang/minlength_msg', 50),
            '^error_maxlength'     => qa_lang_sub('qea_lang/maxlength_msg', 600),
            '^option_required'     => qa_lang('qea_lang/option_required'),
            '^label_select'       => qa_lang('qea_lang/label_select'),
            '^unknown'            => qa_lang('qea_lang/unknown'),
    );
        switch ($form_id) {
            case '1':
                $form_params = array(
                    '^form_head'         => qa_lang('qea_lang/q1_form_head'),
                    '^form_subhead'      => qa_lang('qea_lang/q1_form_subhead'),
                    '^form_message'      => qa_lang('qea_lang/q1_form_message'),
                    '^image'             => qa_lang('qea_lang/q1_image'),
                    '^image_subhead'     => qa_lang('qea_lang/q1_image_subhead'),
                    '^image_alt'         => qa_lang('qea_lang/q1_image_alt'),
                    '^image_required'    => qa_lang('qea_lang/q1_image_required'),
                    '^place_subhead'     => qa_lang('qea_lang/q1_place_subhead'),
                    '^place_placeholder' => qa_lang('qea_lang/q1_place_placeholder'),
                    '^place'             => qa_lang('qea_lang/q1_place'),
                    '^owned'             => qa_lang('qea_lang/q1_owned'),
                    '^owned_subhead'     => qa_lang('qea_lang/q1_owned_subhead'),
                    '^wind'              => qa_lang('qea_lang/q1_wind'),
                    '^wind_subhead'      => qa_lang('qea_lang/q1_wind_subhead'),
                    '^sunlight'          => qa_lang('qea_lang/q1_sunlight'),
                    '^sunlight_subhead'  => qa_lang('qea_lang/q1_sunlight_subhead'),
                    '^pesticide'         => qa_lang('qea_lang/q1_pesticide'),
                    '^pesticide_subhead' => qa_lang('qea_lang/q1_pesticide_subhead'),
                    '^others'            => qa_lang('qea_lang/q1_others'),
                    '^others_subhead'    => qa_lang('qea_lang/q1_others_subhead'),
                    '^comment_subhead'   => qa_lang('qea_lang/q1_comment_subhead'),
                );
                break;
            case 2:
                $form_params = array(
                    '^head'              => qa_lang('qea_lang/q2_form_head'),
                    '^message'           => qa_lang('qea_lang/q2_form_message'),
                    '^experience'        => qa_lang('qea_lang/q2_experience'),
                    '^hive_type'         => qa_lang('qea_lang/q2_hive_type'),
                    '^label_hive_type'   => qa_lang('qea_lang/q2_hive_type_label'),
                    '^hive_num'          => qa_lang('qea_lang/q2_hive_num'),
                    '^label_hive_num'    => qa_lang('qea_lang/q2_hive_num_label'),
                    '^hive_place_subhead' => qa_lang('qea_lang/q2_hive_place_subhead'),
                    '^hive_place'        => qa_lang('qea_lang/q2_hive_place'),
                    '^label_hive_place'  => qa_lang('qea_lang/q2_hive_place_label'),
                    '^beeswax'           => qa_lang('qea_lang/q2_beeswax'),
                    '^use_lure'          => qa_lang('qea_lang/q2_use_lure'),
                    '^label_use_lure'    => qa_lang('qea_lang/q2_use_lure_label'),
                    '^kinryohen'         => qa_lang('qea_lang/q2_kinryohen'),
                    '^label_kinryohen'   => qa_lang('qea_lang/q2_kinryohen_label'),
                    '^comment_subhead'   => qa_lang('qea_lang/q2_comment_subhead'),
                    '^image_subhead'     => qa_lang('qea_lang/q2_image_subhead'),
                    '^image'             => qa_lang('qea_lang/q2_image'),
                    '^hive_type_1'       => qa_lang('qea_lang/q2_hive_type_1'),
                    '^hive_type_2'       => qa_lang('qea_lang/q2_hive_type_2'),
                    '^hive_type_3'       => qa_lang('qea_lang/q2_hive_type_3'),
                    '^hive_type_4'       => qa_lang('qea_lang/q2_hive_type_4'),
                    '^hive_type_5'       => qa_lang('qea_lang/q2_hive_type_5'),
                    '^option_0'          => qa_lang('qea_lang/q2_option_0'),
                    '^option_1'          => qa_lang('qea_lang/q2_option_1'),
                    '^option_2'          => qa_lang('qea_lang/q2_option_2'),
                    '^option_3'          => qa_lang('qea_lang/q2_option_3'),
                    '^option_4'          => qa_lang('qea_lang/q2_option_4'),
                    '^option_5'          => qa_lang('qea_lang/q2_option_5'),
                    '^option_6'          => qa_lang('qea_lang/q2_option_6'),
                );
                break;
            case 3:
                $form_params = array(
                    '^head'              => qa_lang('qea_lang/q3_form_head'),
                    '^message'           => qa_lang('qea_lang/q3_form_message'),
                    '^inspect_date'      => qa_lang('qea_lang/q3_inspect_date'),
                    '^inspect_time'      => qa_lang('qea_lang/q3_inspect_time'),
                    '^label_inspect_time' => qa_lang('qea_lang/q3_label_inspect_time'),
                    '^temp_weather'      => qa_lang('qea_lang/q3_temp_weather'),
                    '^label_temp_weather' => qa_lang('qea_lang/q3_label_temp_weather'),
                    '^when_breed' => qa_lang('qea_lang/q3_when_breed'),
                    '^label_when_breed' => qa_lang('qea_lang/q3_label_when_breed'),
                    '^ph_when_breed' => qa_lang('qea_lang/q3_ph_when_breed'),
                    '^enter_exit'       => qa_lang('qea_lang/q3_enter_exit'),
                    '^pollen'       => qa_lang('qea_lang/q3_pollen'),
                    '^hive_size'       => qa_lang('qea_lang/q3_hive_size'),
                    '^label_hive_size'       => qa_lang('qea_lang/q3_label_hive_size'),
                    '^ph_hive_size'       => qa_lang('qea_lang/q3_ph_hive_size'),
                    '^growing'       => qa_lang('qea_lang/q3_growing'),
                    '^label_growing'       => qa_lang('qea_lang/q3_label_growing'),
                    '^ph_growing'       => qa_lang('qea_lang/q3_ph_growing'),
                    '^scrap'       => qa_lang('qea_lang/q3_scrap'),
                    '^sumushi'       => qa_lang('qea_lang/q3_sumushi'),
                    '^discard'       => qa_lang('qea_lang/q3_discard'),
                    '^drone'       => qa_lang('qea_lang/q3_drone'),
                    '^overflow'       => qa_lang('qea_lang/q3_overflow'),
                    '^wander'       => qa_lang('qea_lang/q3_wander'),
                    '^menthol'       => qa_lang('qea_lang/q3_menthol'),
                    '^collect'       => qa_lang('qea_lang/q3_collect'),
                    '^ph_comment'       => qa_lang('qea_lang/q3_ph_comment'),
                    '^inner_image'       => qa_lang('qea_lang/q3_inner_image'),
                    '^image'       => qa_lang('qea_lang/q3_image'),
                    '^sub_image'       => qa_lang('qea_lang/q3_sub_image'),
                    '^other'       => qa_lang('qea_lang/other'),
                );
                break;
            case 4:
                $form_params = array(
                    '^head'              => qa_lang('qea_lang/q4_form_head'),
                    '^message'           => qa_lang('qea_lang/q4_form_message'),
                    '^place'             => qa_lang('qea_lang/q4_place'),
                    '^label_place'       => qa_lang('qea_lang/q4_label_place'),
                    '^ph_place'          => qa_lang('qea_lang/q4_ph_place'),
                    '^petals'            => qa_lang('qea_lang/q4_petals'),
                    '^where_putting'     => qa_lang('qea_lang/q4_where_putting'),
                    '^label_where_putting' => qa_lang('qea_lang/q4_label_where_putting'),
                    '^ph_where_putting'  => qa_lang('qea_lang/q4_ph_where_putting'),
                    '^temp'              => qa_lang('qea_lang/q4_temp'),
                    '^label_temp'        => qa_lang('qea_lang/q4_label_temp'),
                    '^ph_temp'           => qa_lang('qea_lang/q4_ph_temp'),
                    '^sun'               => qa_lang('qea_lang/q4_sun'),
                    '^label_sun'         => qa_lang('qea_lang/q4_label_sun'),
                    '^ph_sun'            => qa_lang('qea_lang/q4_ph_sun'),
                    '^water'             => qa_lang('qea_lang/q4_water'),
                    '^label_water'       => qa_lang('qea_lang/q4_label_water'),
                    '^ph_water'          => qa_lang('qea_lang/q4_ph_water'),
                    '^fertilizer'        => qa_lang('qea_lang/q4_fertilizer'),
                    '^label_fertilizer'  => qa_lang('qea_lang/q4_label_fertilizer'),
                    '^ph_fertilizer'     => qa_lang('qea_lang/q4_ph_fertilizer'),
                    '^when_bloom'        => qa_lang('qea_lang/q4_when_bloom'),
                    '^option_1'          => qa_lang('qea_lang/q4_option_1'),
                    '^option_2'          => qa_lang('qea_lang/q4_option_2'),
                    '^option_3'          => qa_lang('qea_lang/q4_option_3'),
                    '^option_4'          => qa_lang('qea_lang/q4_option_4'),
                    '^option_5'          => qa_lang('qea_lang/q4_option_5'),
                    '^option_6'          => qa_lang('qea_lang/q4_option_6'),
                    '^option_7'          => qa_lang('qea_lang/q4_option_7'),
                    '^plan'              => qa_lang('qea_lang/q4_plan'),
                    '^label_plan'        => qa_lang('qea_lang/q4_label_plan'),
                    '^image'             => qa_lang('qea_lang/q4_image'),
                    '^sub_image'         => qa_lang('qea_lang/q4_sub_image'),
                );
                break;
            default:
                $form_params = array();
        }
        $params = array_merge($common_params, $form_params);

        return $params;
    }

    private function no_login_message()
    {
        $this->output('<div class="mdl-card mdl-cell mdl-cell--12-col">
  <div class="mdl-card__supporting-text">');
        $this->output(qa_lang('qea_lang/no_login_message'));
        $this->output('</div></div>');
    }

    private function get_js_lang($form_id)
    {
        $url = '/easy-ask/' . $form_id;

        $common_lang = array(
            'confirm_title'    => qa_lang('qea_lang/confirm_title'),
            'confirm_content'  => qa_lang('qea_lang/confirm_content'),
            'label_post'       => qa_lang('qea_lang/label_post'),
            'label_cancel'     => qa_lang('qea_lang/label_cancel'),
            'label_close'      => qa_lang('qea_lang/label_close'),
            'error_title'      => qa_lang('qea_lang/error_title'),
            'error_msg'        => qa_lang('qea_lang/error_msg'),
            'comment'          => qa_lang('qea_lang/comment'),
            'question_footer'  => qa_lang_sub('qea_lang/question_footer', $url),
            'date_format'  => qa_lang('qea_lang/date_format'),
        );
        $form_lang = array();
        $handle = qa_get_logged_in_handle();
        
        switch ($form_id) {
            case '1':
                $form_lang = array(
                    'q1_title'         => qa_lang('qea_lang/q1_title'),
                    'q1_place'         => qa_lang('qea_lang/q1_place'),
                    'q1_owned'         => qa_lang('qea_lang/q1_owned'),
                    'q1_wind'          => qa_lang('qea_lang/q1_wind'),
                    'q1_sunlight'      => qa_lang('qea_lang/q1_sunlight'),
                    'q1_pesticide'     => qa_lang('qea_lang/q1_pesticide'),
                    'q1_others'        => qa_lang('qea_lang/q1_others'),
                );
                break;
            case '2':
                $title = qa_lang_sub('qea_lang/q2_title', $handle);
                $form_lang = array(
                    'title' => $title,
                    'content_head' => qa_lang_sub('qea_lang/q2_content_head', $handle),
                    'experience' => qa_lang('qea_lang/q2_experience'),
                    'hive_type' => qa_lang('qea_lang/q2_hive_type'),
                    'hive_num' => qa_lang('qea_lang/q2_hive_num'),
                    'hive_place' => qa_lang('qea_lang/q2_hive_place'),
                    'beeswax' => qa_lang('qea_lang/q2_beeswax'),
                    'use_lure' => qa_lang('qea_lang/q2_use_lure'),
                    'kinryohen' => qa_lang('qea_lang/q2_kinryohen'),
                );
                break;
            case '3':
                $form_lang = array(
                    'title' => qa_lang('qea_lang/q3_title'),
                    'content_head' => qa_lang_sub('qea_lang/q3_content_head', $handl),
                    'inspect_date' => qa_lang('qea_lang/q3_inspect_date'),
                    'inspect_time' => qa_lang('qea_lang/q3_inspect_time'),
                    'temp_weather' => qa_lang('qea_lang/q3_temp_weather'),
                    'when_breed'   => qa_lang('qea_lang/q3_when_breed'),
                    'enter_exit'   => qa_lang('qea_lang/q3_enter_exit'),
                    'pollen'       => qa_lang('qea_lang/q3_pollen'),
                    'hive_size'    => qa_lang('qea_lang/q3_hive_size'),
                    'growing'      => qa_lang('qea_lang/q3_growing'),
                    'scrap'        => qa_lang('qea_lang/q3_scrap'),
                    'sumushi'      => qa_lang('qea_lang/q3_sumushi'),
                    'discard'      => qa_lang('qea_lang/q3_discard'),
                    'drone'        => qa_lang('qea_lang/q3_drone'),
                    'overflow'     => qa_lang('qea_lang/q3_overflow'),
                    'wander'       => qa_lang('qea_lang/q3_wander'),
                    'menthol'      => qa_lang('qea_lang/q3_menthol'),
                    'collect'      => qa_lang('qea_lang/q3_collect'),
                    'inner_image'  => qa_lang('qea_lang/q3_inner_image_caption'),
                    'image'        => qa_lang('qea_lang/q3_image'),
                );
                break;
            case '4':
                $form_lang = array(
                    'title' => qa_lang('qea_lang/q4_title'),
                    'content_head' => qa_lang_sub('qea_lang/q4_content_head', $handl),
                    'place'        => qa_lang('qea_lang/q4_place'),
                    'petals'       => qa_lang('qea_lang/q4_petals'),
                    'where_putting' => qa_lang('qea_lang/q4_where_putting'),
                    'temp'         => qa_lang('qea_lang/q4_temp'),
                    'sun'          => qa_lang('qea_lang/q4_sun'),
                    'water'        => qa_lang('qea_lang/q4_water'),
                    'fertilizer'   => qa_lang('qea_lang/q4_fertilizer'),
                    'when_bloom'   => qa_lang('qea_lang/q4_when_bloom'),
                    'plan'         => qa_lang('qea_lang/q4_plan'),
                    'image'        => qa_lang('qea_lang/q4_image'),
                );
                break;
            default:
                $form_lang = array();
        }

        $jslang = array_merge($common_lang, $form_lang);
        return $jslang;
    }

    private function output_prev_question($question)
    {
        $path = QEA_DIR.'/html/prev_question.html';
        include $path;
    }

    private function get_prev_question($userid, $min = 30)
    {
        $sql = "SELECT postid, title, ";
        $sql .= " TIME_FORMAT(TIMEDIFF(DATE_ADD(created, INTERVAL # MINUTE), NOW()), '%i') AS remains";
        $sql .= " FROM ^posts";
        $sql .= " WHERE TYPE = 'Q'";
        $sql .= " AND userid = $";
        $sql .= " AND created > DATE_SUB(NOW(), INTERVAL # MINUTE)";
        return qa_db_read_one_assoc(qa_db_query_sub($sql, $min, $userid, $min), true);
    }
}
