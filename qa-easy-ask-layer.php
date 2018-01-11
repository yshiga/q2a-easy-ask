<?php

class qa_html_theme_layer extends qa_html_theme_base
{
    public function head_css()
    {
        qa_html_theme_base::head_css();
        if ($this->template==='easy-ask') {
            $styles =<<<EOS
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic">
EOS;
            $this->output($styles);
            $css = '';
            $this->output('<LINK REL="stylesheet" TYPE="text/css" HREF="'.QA_HTML_THEME_LAYER_URLTOROOT.'css/easy-ask.css"/>');
            $this->output($css);
        }
    }

    function main_parts($content)
    {
        if($this->template === 'easy-ask') {
            $form_id = qa_request_part(1);
            $form_template = QEA_DIR.'/html/form_'.$form_id.'.html';
            if (file_exists($form_template)) {
                $tmpl = file_get_contents($form_template);
                $url = QA_HTML_THEME_LAYER_URLTOROOT;
                $html = strtr($tmpl, array(
                    '^url' => $url
                ));
                $this->output($html);
            } else {
                $this->output( 'form not found!!!' );
            }
        } else {
            qa_html_theme_base::main_parts($content);
        }
    }
}
