<?php

class qa_easy_ask_page {

    var $directory;
    var $urltoroot;


    function load_module($directory, $urltoroot)
    {
        $this->directory=$directory;
        $this->urltoroot=$urltoroot;
    }


    function suggest_requests() // for display in admin interface
    {
        return array(
            array(
                'title' => 'Easy Ask Page',
                'request' => 'easy-ask',
                'nav' => 'none', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
            ),
        );
    }


    function match_request($request)
    {
        $requestparts = qa_request_parts();

        return ($requestparts[0] === 'easy-ask' && isset($requestparts[1]));
    }


    function process_request($request)
    {
        qa_set_template('easy-ask');
        $qa_content = qa_content_prepare();
        $qa_content['title'] = qa_lang('qea_lang/page_title');
        $qa_content['security_code'] = qa_get_form_security_code('easy-ask');
        return $qa_content;
    }

}


/*
    Omit PHP closing tag to help avoid accidental output
*/
