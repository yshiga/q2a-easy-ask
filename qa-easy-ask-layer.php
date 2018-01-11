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
            <style>

            .file-input {
                display: none;
            }

            .md-button label {
                display: inline-block;
                width: 100%;
                cursor: pointer;
            }

            .wrap-border {
                border: 1px solid #ddd;
            }
        
            .logo img {
                width: 150px;
            }
        
            .text-size-110 {
                font-size: 110%;
            }
        
            .photo-upload.error {
                border-width: 2px;
                border-color: rgb(211, 47, 47);
            }
        
            md-radio-button {
                border: 1px solid #aaa;
                border-radius: 3px;
                padding: 8px;
                min-width: 120px;
            }
        
            md-radio-button.error {
                background: rgb(183, 28, 28);
                border-color: rgb(183, 28, 28);
            }
        
            md-radio-button:hover,
            md-radio-button.md-checked {
                border-color: rgba(255, 171, 64, 0.87);
                background: rgba(255, 171, 64, 0.2);
            }
        
            md-radio-button .md-container {
                left: 8px;
            }
        
            @media (max-width: 374px) and (min-width: 0) and (orientation: portrait) {
                .md-toolbar-tools {
                height: 76px;
                max-height: 76px;
                }
            }
            </style>
EOS;
            $this->output($styles);
        }
    }

    function main_parts($content)
    {
        if($this->template === 'easy-ask') {
            $form_id = qa_request_part(1);
            $form_template = QEA_DIR.'/html/form_'.$form_id.'.html';
            if (file_exists($form_template)) {
                $tmpl = file_get_contents($form_template);
                echo $tmpl;
                $url = qa_path_to_root().basename( QA_PLUGIN_DIR ).DIRECTORY_SEPARATOR.QEA_FOLDER;
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
