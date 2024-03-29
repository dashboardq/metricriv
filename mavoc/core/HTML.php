<?php

namespace mavoc\core;

class HTML {
    public $req;
    public $res;
    public $session;

    public function __construct() {
    }   

    public function init() {
    }

    public function _a($href, $value = '', $class = '') {
        if($value == '') {
            $value = 'Link';
        }
        $output = '';
        $output .= '<a href="' . _uri($href) . '" ';
        $output .= 'class="' . $class . '" ';
        $output .= '>';
        $output .= _esc($value);
        $output .= '</a>';
        $output .= "\n";

        return $output;
    }
    public function a($action, $value = '', $class = '') {
        $output = $this->_a($action, $value, $class);
        echo $output;
    }

    public function _delete($action, $value = '', $class = '', $warning = '') {
        if($value == '') {
            $value = 'Delete';
        }
        if($warning == '') {
            $warning = 'Are you sure you want to delete this item?';
        }
        $output = '';
        $output .= '<form action="' . _uri($action) . '" method="POST">';
        $output .= "\n";
        $output .= $this->_submit($value, $class, 'onclick="return confirm(\'' . $warning . '\');"');
        $output .= "\n";
        $output .= '</form>';
        $output .= "\n";

        return $output;
    }
    public function delete($action, $value = '', $class = '', $warning = '') {
        $output = $this->_delete($action, $value, $class, $warning);
        echo $output;
    }

    public function _hidden($name, $value) {
        if(isset($this->session->flash['fields'][$name])) {
            $value = $this->session->flash['fields'][$name];
        } elseif(isset($this->res->fields[$name])) {
            $value = $this->res->fields[$name];
        }

        $output = '';
        $output .= '<input type="hidden" name="' . _esc($name) . '" value="' . _esc($value) . '" ';
        $output .= '/> ';
        $output .= "\n";

        return $output;
    }
    public function hidden($name, $value) {
        $output = $this->_hidden($name, $value);
        echo $output;
    }

    public function _link($url, $name = '', $class = '') {
        if($name == '') {
            $name = $url;
        }

        $output = '';
        $output .= '<a href="' . _url($url) . '" ';
        $output .= 'class="' . $class . '" ';
        $output .= '> ';
        $output .= _esc($name);
        $output .= '</a>';
        $output .= "\n";

        return $output;
    }
    public function link($url, $name = '', $class = '') {
        $output = $this->_link($url, $name, $class);
        echo $output;
    }

    public function _messages() {
        $output = '';
        if(isset($this->session->flash['error'])) {
            $output .= '<div class="notice error">';
            $output .= "\n";
            foreach($this->session->flash['error'] as $field => $messages) {
                foreach($messages as $message) {
                    $output .= '<p>' . _esc($message) . '</p>';
                    $output .= "\n";
                }
            }
            $output .= '</div>';
            $output .= "\n";
        }
        if(isset($this->session->flash['success'])) {
            $output .= '<div class="notice success">';
            $output .= "\n";
            foreach($this->session->flash['success'] as $field => $messages) {
                foreach($messages as $message) {
                    $output .= '<p>' . _esc($message) . '</p>';
                    $output .= "\n";
                }
            }
            $output .= '</div>';
            $output .= "\n";
        }

        return $output;
    }

    public function messages() {
        $output = $this->_messages();
        echo $output;
    }

    public function _option($label, $name = '', $value = '', $class = '', $extra = '') {
        if($value === '') {
            $value = underscorify($label);
        }

        $selected = '';
        if(
            isset($this->session->flash['fields'][$name]) 
            && $value == $this->session->flash['fields'][$name]
        ) {
            $selected = 'selected ';
        } elseif(
            isset($this->res->fields[$name])
            && $value == $this->res->fields[$name]
        ) {
            $selected = 'selected ';
        }

        $output = '';
        $output .= '<option value="' . _esc($value) . '" ';
        $output .= 'class="' . $class . '" ';
        $output .= $selected;
        // Be careful with $extra values - they are not escaped.
        // Do not use untrusted data.
        if($extra) {
            $output .= $extra;
        }
        $output .= ' />';
        $output .= _esc($label);
        $output .= '</option>';
        $output .= "\n";

        return $output;
    }

    public function _password($label, $name = '') {
        if(!$name) {
            $name = underscorify($label);
        }

        $value = '';
        if(isset($this->session->flash['fields'][$name])) {
            $value = $this->session->flash['fields'][$name];
        }

        $error = false;
        if(isset($this->session->flash['error'][$name])) {
            $error = true;
        }

        $output = '';
        if($error) {
            $output .= '<div class="field -error">';
        } else {
            $output .= '<div class="field">';
        }
        $output .= "\n";
        $output .= '<label>' . _esc($label) . '</label>';
        $output .= "\n";
        $output .= '<input type="password" name="' . _esc($name) . '" value="' . _esc($value) . '" placeholder="' . _esc($label) . '" />';
        $output .= "\n";
        $output .= '</div>';
        $output .= "\n";

        return $output;
    }

    public function password($label, $name = '') {
        $output = $this->_password($label, $name);
        echo $output;
    }

    public function _post($action, $value = '', $class = '', $warning = '') {
        if($value == '') {
            $value = 'Update';
        }

        $output = '';
        $output .= '<form action="' . _uri($action) . '" method="POST">';
        $output .= "\n";
        if($warning) {
            $output .= $this->_submit($value, $class, 'onclick="return confirm(\'' . $warning . '\');"');
        } else {
            $output .= $this->_submit($value, $class);
        }
        $output .= "\n";
        $output .= '</form>';
        $output .= "\n";

        return $output;
    }
    public function post($action, $value = '', $class = '', $warning = '') {
        $output = $this->_post($action, $value, $class, $warning);
        echo $output;
    }

    public function _radio($label, $name = '', $value = '', $class = '', $extra = '') {
        if($value === '') {
            $value = underscorify($label);
        }

        $checked = '';
        if(
            isset($this->session->flash['fields'][$name]) 
            && $value == $this->session->flash['fields'][$name]
        ) {
            $checked = 'checked ';
        } elseif(
            isset($this->res->fields[$name])
            && $value == $this->res->fields[$name]
        ) {
            $checked = 'checked ';
        }

        $output = '';
        $output .= '<label>';
        $output .= '<input type="radio" name="' . _esc($name) . '" value="' . _esc($value) . '" ';
        $output .= 'class="' . $class . '" ';
        $output .= $checked;
        // Be careful with $extra values - they are not escaped.
        // Do not use untrusted data.
        if($extra) {
            $output .= $extra;
        }
        $output .= ' /> ';
        $output .= _esc($label);
        $output .= '</label>';
        $output .= "\n";

        return $output;
    }
    public function radio($label, $name = '', $value = '', $class = '', $extra = '') {
        $output = $this->_radio($label, $name, $value, $class, $extra);
        echo $output;
    }
    public function _radioRaw($name, $value = '', $class = '', $extra = '') {
        if($value === '') {
            $value = 'yes';
        }

        $checked = '';
        if(
            isset($this->session->flash['fields'][$name]) 
            && $value == $this->session->flash['fields'][$name]
        ) {
            $checked = 'checked ';
        } elseif(
            isset($this->res->fields[$name])
            && $value == $this->res->fields[$name]
        ) {
            $checked = 'checked ';
        }

        $output = '';
        $output .= '<input type="radio" name="' . _esc($name) . '" value="' . _esc($value) . '" ';
        $output .= 'class="' . $class . '" ';
        $output .= $checked;
        // Be careful with $extra values - they are not escaped.
        // Do not use untrusted data.
        if($extra) {
            $output .= $extra;
        }
        $output .= ' /> ';
        $output .= "\n";

        return $output;
    }
    public function radioRaw($name = '', $value = '', $class = '', $extra = '') {
        $output = $this->_radioRaw($name, $value, $class, $extra);
        echo $output;
    }

    public function _radios($label, $name = '', $data = []) {
        $error = false;
        if(isset($this->session->flash['error'][$name])) {
            $error = true;
        }

        $output = '';
        if($error) {
            $output .= '<div class="field -error">';
        } else {
            $output .= '<div class="field">';
        }
        $output .= "\n";
        $output .= '<label>' . _esc($label) . '</label>';
        $output .= "\n";

        foreach($data as $item) {
            if(isset($item['label'])) {
                $output .= $this->_radio($item['label'], $name, $item['value'] ?? '', $item['class'] ?? '', $item['extra'] ?? '');
            } else {
                $output .= $this->_radio($item, $name, $item);
            }
        }

        $output .= '</div>';
        $output .= "\n";


        return $output;
    }
    public function radios($label, $name = '', $data = []) {
        $output = $this->_radios($label, $name, $data);
        echo $output;
    }

    public function _select($label, $name = '', $data = []) {
        $error = false;
        if(isset($this->session->flash['error'][$name])) {
            $error = true;
        }

        $output = '';
        if($error) {
            $output .= '<div class="field -error">';
        } else {
            $output .= '<div class="field">';
        }
        $output .= "\n";
        $output .= '<label>' . _esc($label) . '</label>';
        $output .= "\n";
        $output .= '<select name="' . _esc($name) . '">';
        $output .= "\n";

        foreach($data as $item) {
            if(isset($item['label'])) {
                $output .= $this->_option($item['label'], $name, $item['value'] ?? '', $item['class'] ?? '', $item['extra'] ?? '');
            } else {
                $output .= $this->_option($item, $name, $item);
            }
        }

        $output .= '</select>';
        $output .= "\n";

        $output .= '</div>';
        $output .= "\n";


        return $output;
    }
    public function select($label, $name = '', $data = []) {
        $output = $this->_select($label, $name, $data);
        echo $output;
    }

    public function _selectRaw($name = '', $data = []) {
        $error = false;
        if(isset($this->session->flash['error'][$name])) {
            $error = true;
        }

        $output = '';
        $output .= '<select name="' . _esc($name) . '">';
        $output .= "\n";

        foreach($data as $item) {
            $output .= $this->_option($item['label'], $name, $item['value'] ?? '', $item['class'] ?? '', $item['extra'] ?? '');
        }

        $output .= '</select>';
        $output .= "\n";


        return $output;
    }
    public function selectRaw($name = '', $data = []) {
        $output = $this->_selectRaw($name, $data);
        echo $output;
    }


    public function _submit($value, $class = '', $extra = '') {
        $output = '';
        $output .= '<div class="field">';
        $output .= "\n";
        $output .= '<input type="submit" ';
        $output .= 'class="' . $class . '" ';
        $output .= 'value="' . _esc($value) . '" ';
        // Be careful with $extra values - they are not escaped.
        // Do not use untrusted data.
        if($extra) {
            $output .= $extra;
        }
        $output .= ' />';
        $output .= "\n";
        $output .= '</div>';
        $output .= "\n";

        return $output;
    }

    public function submit($value, $class = '', $extra = '') {
        $output = $this->_submit($value, $class, $extra);
        echo $output;
    }

    public function _text($label, $name = '', $value = '', $class = '', $extra = '') {
        if(!$name) {
            $name = underscorify($label);
        }

        if(isset($this->session->flash['fields'][$name])) {
            $value = $this->session->flash['fields'][$name];
        } elseif(isset($this->res->fields[$name])) {
            $value = $this->res->fields[$name];
        }

        $error = false;
        if(isset($this->session->flash['error'][$name])) {
            $error = true;
        }

        $output = '';
        if($error) {
            $output .= '<div class="field -error">';
        } else {
            $output .= '<div class="field">';
        }
        $output .= "\n";
        $output .= '<label>' . _esc($label) . '</label>';
        $output .= "\n";
        $output .= '<input type="text" name="' . _esc($name) . '" value="' . _esc($value) . '" placeholder="' . _esc($label) . '" ';
        $output .= 'class="' . $class . '" ';
        // Be careful with $extra values - they are not escaped.
        // Do not use untrusted data.
        if($extra) {
            $output .= $extra;
        }
        $output .= ' /> ';
        $output .= "\n";
        $output .= '</div>';
        $output .= "\n";

        return $output;
    }
    public function text($label, $name = '', $value = '', $class = '', $extra = '') {
        $output = $this->_text($label, $name, $value, $class, $extra);
        echo $output;
    }
    public function _textRaw($label, $name = '', $value = '', $class = '', $extra = '') {
        if(!$name) {
            $name = underscorify($label);
        }

        if(isset($this->session->flash['fields'][$name])) {
            $value = $this->session->flash['fields'][$name];
        } elseif(isset($this->res->fields[$name])) {
            $value = $this->res->fields[$name];
        }

        $error = false;
        if(isset($this->session->flash['error'][$name])) {
            $error = true;
        }

        $output = '';
        $output .= '<input type="text" name="' . _esc($name) . '" value="' . _esc($value) . '" placeholder="' . _esc($label) . '" ';
        $output .= 'class="' . $class . '" ';
        // Be careful with $extra values - they are not escaped.
        // Do not use untrusted data.
        if($extra) {
            $output .= $extra;
        }
        $output .= ' /> ';
        $output .= "\n";

        return $output;
    }
    public function textRaw($label, $name = '', $value = '', $class = '', $extra = '') {
        $output = $this->_textRaw($label, $name, $value, $class, $extra);
        echo $output;
    }

    public function _textarea($label, $name = '', $value = '') {
        if(!$name) {
            $name = underscorify($label);
        }

        if(isset($this->session->flash['fields'][$name])) {
            $value = $this->session->flash['fields'][$name];
        }

        $error = false;
        if(isset($this->session->flash['error'][$name])) {
            $error = true;
        }

        $output = '';
        if($error) {
            $output .= '<div class="field -error">';
        } else {
            $output .= '<div class="field">';
        }
        $output .= "\n";
        $output .= '<label>' . _esc($label) . '</label>';
        $output .= "\n";
        $output .= '<textarea type="text" name="' . _esc($name) . '" placeholder="' . _esc($label) . '">';
        $output .= _esc($value);
        $output .= '</textarea>';
        $output .= "\n";
        $output .= '</div>';
        $output .= "\n";

        return $output;
    }

    public function textarea($label, $name = '', $value = '') {
        $output = $this->_textarea($label, $name, $value);
        echo $output;
    }

}
