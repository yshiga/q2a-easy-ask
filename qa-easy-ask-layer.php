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
            $this->output('<script src="'.$url.'js/easy-ask_'.$form_id.'.js"></script>');
        }
    }

    public function main_parts($content)
    {
        if($this->template === 'easy-ask') {
            if (!qa_is_logged_in()) {
                $this->no_login_message();
            } else {
                $form_id = qa_request_part(1);
                $form_template = QEA_DIR.'/html/form_'.$form_id.'.html';
                if (file_exists($form_template)) {
                    $tmpl = file_get_contents($form_template);
                    $url = QA_HTML_THEME_LAYER_URLTOROOT;
                    $html = strtr($tmpl, array(
                        '^url' => $url,
                        '^code' => $this->content['security_code']
                    ));
                    $this->output($html);
                } else {
                    $this->output( 'form not found!!!' );
                }
            }
        } else {
            qa_html_theme_base::main_parts($content);
        }
    }

    private function no_login_message()
    {
        $this->output('<div id="top_ad_container" class="mdl-card mdl-cell mdl-cell--12-col">
  <div class="mdl-card__supporting-text">');
        $this->output(qa_lang('qea_lang/no_login_message'));
        $this->output('</div></div>');
    }
}
