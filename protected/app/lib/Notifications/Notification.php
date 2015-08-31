<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 26/07/15
 * Time: 5:48 PM
 */

namespace Notifications;


class Notification {

    public $messageTemplate;
    public $subjectTemplate;
    public $data;

    public function __construct($messageTemplate, $subjectTemplate, $data) {
        $this->messageTemplate = $messageTemplate;
        $this->subjectTemplate = $subjectTemplate;
        $this->data = $data;
    }

    public function renderMessage() {
        return $this->_renderTemplate($this->messageTemplate);
    }

    public function renderSubject() {
        return $this->_renderTemplate($this->subjectTemplate);
    }

    public function _renderTemplate($template) {
        $output = $template;
        foreach($this->data as $key => $val) {
            $output = str_replace('[' . $key . ']', $val, $output);
        }
        return $output;
    }
}