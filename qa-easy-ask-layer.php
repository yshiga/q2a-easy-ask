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
</script>
EOS2;
            $this->output($jsvar);
            $this->output('<script src="'.$url.'js/easy-ask_'.$form_id.'.js"></script>');
        }
    }

    public function main_parts($content)
    {
        require_once QA_THEME_DIR . qa_opt('site_theme') . '/qa-theme-utils.php';

        if($this->template === 'easy-ask') {
            if (!qa_is_logged_in()) {
                $this->no_login_message();
            } else {
                $userid = qa_get_logged_in_userid();
                $prev_question = qa_theme_utils::get_prev_question($userid);
                if (!empty($prev_question)) {
                    $this->output_prev_question($prev_question);
                    return;
                }
                $form_id = qa_request_part(1);
                $form_template = QEA_DIR.'/html/form_'.$form_id.'.html';
                if (file_exists($form_template)) {
                    $tmpl = file_get_contents($form_template);
                    $params = $this->get_params();
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

    private function get_params()
    {
        $url = QA_HTML_THEME_LAYER_URLTOROOT;
        $file_max_size_mb = number_format(qa_opt('medium_editor_upload_max_size') / 1048576, 0) . 'MB';
        return array(
            '^url'               => $url,
            '^page_title'        => $this->content['title'],
            '^form_head'         => qa_lang('qea_lang/q1_form_head'),
            '^form_subhead'      => qa_lang('qea_lang/q1_form_subhead'),
            '^form_message'      => qa_lang('qea_lang/q1_form_message'),
            '^required'          => qa_lang('qea_lang/required'),
            '^optional'          => qa_lang('qea_lang/optional'),
            '^image'             => qa_lang('qea_lang/q1_image'),
            '^image_subhead'     => qa_lang('qea_lang/q1_image_subhead'),
            '^image_alt'         => qa_lang('qea_lang/q1_image_alt'),
            '^add_image'         => qa_lang('qea_lang/add_image'),
            '^delete_image'      => qa_lang('qea_lang/delete_image'),
            '^image_required'    => qa_lang('qea_lang/q1_image_required'),
            '^place_subhead'     => qa_lang('qea_lang/q1_place_subhead'),
            '^place_placeholder' => qa_lang('qea_lang/q1_place_placeholder'),
            '^place_required'    => qa_lang('qea_lang/q1_place_required'),
            '^place_minlength'   => qa_lang('qea_lang/q1_place_minlength'),
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
            '^comment_required'  => qa_lang('qea_lang/q1_comment_required'),
            '^comment'           => qa_lang('qea_lang/comment'),
            '^yes'               => qa_lang('qea_lang/yes'),
            '^no'                => qa_lang('qea_lang/no'),
            '^required_select'   => qa_lang('qea_lang/q1_required_select'),
            '^post_complete'     => qa_lang('qea_lang/post_complete'),
            '^post_comp_msg'     => qa_lang('qea_lang/post_comp_msg'),
            '^check_button_label' => qa_lang('qea_lang/check_button_label'),
            '^post'              => qa_lang('qea_lang/post'),
            '^file_max_size'      => $file_max_size_mb,
            '^file_max_size_error' => qa_lang_sub('qea_lang/file_max_size_error', $file_max_size_mb),
            '^file_type_error'   => qa_lang('qea_lang/file_type_error'),
        );
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
        return array(
            'confirm_title'    => qa_lang('qea_lang/confirm_title'),
            'confirm_content'  => qa_lang('qea_lang/confirm_content'),
            'q1_title'         => qa_lang('qea_lang/q1_title'),
            'q1_place'         => qa_lang('qea_lang/q1_place'),
            'q1_owned'         => qa_lang('qea_lang/q1_owned'),
            'q1_wind'          => qa_lang('qea_lang/q1_wind'),
            'q1_sunlight'      => qa_lang('qea_lang/q1_sunlight'),
            'q1_pesticide'     => qa_lang('qea_lang/q1_pesticide'),
            'q1_others'        => qa_lang('qea_lang/q1_others'),
            'comment'          => qa_lang('qea_lang/comment'),
            'question_footer'  => qa_lang_sub('qea_lang/question_footer', $url),
            'label_post'       => qa_lang('qea_lang/label_post'),
            'label_cancel'     => qa_lang('qea_lang/label_cancel'),
            'label_close'      => qa_lang('qea_lang/label_close'),
            'error_title'      => qa_lang('qea_lang/error_title'),
            'error_msg'        => qa_lang('qea_lang/error_msg'),
        );
    }

    private function output_prev_question($question)
    {
        $path = QEA_DIR.'/html/prev_question.html';
        include $path;
    }
}
