<?php

/**
 * MeioUploadHelper to display the existing file/image
 */
App::import('Helper', 'Form');

class MeioUploadHelper extends AppHelper {

    var $helpers = array('Form', 'Html');
    var $__default_options = array(
        'fields' => array(
            'dir' => 'dir',
            'filesize' => 'filesize',
            'realname' => 'realname',
            'mimetype' => 'mimetype'
        ),
        'thumbsize' => 'normal',
        'thumbsize_link' => false,
        'show_realname' => true,
        'show_filesize' => false,
        'link_text' => 'Download file',
        'type' => 'file',
        'full' => false,
        'default' => false,
    );

    function displayFile($fieldName, $options = array()) {
        //$this->setEntity($fieldName);

        $options = array_merge(
            array('before' => null, 'between' => null, 'after' => null, 'format' => null),
            $this->Form->_inputDefaults,
            $this->__default_options,
            $options
        );

        $data = $this->data;
        if ( isset($options['data']) ) {
            $data = $options['data'];
        }
        if ( isset($this->params['models']) AND is_array($this->params['models']) ) {
            $modelKey = $this->params['models'][0];
            $fileData = $data[$modelKey];
        }
        if ( isset($options['fileData']) ) {
            $fileData = $options['fileData'];
        }

        $divOptions = array();
        $div = $this->Form->_extractOption('div', $options, true);
        unset($options['div']);

        if (!empty($div)) {
            $divOptions['class'] = 'input';
            $divOptions = $this->addClass($divOptions, $options['type']);
            if (is_string($div)) {
                $divOptions['class'] = $div;
            } elseif (is_array($div)) {
                $divOptions = array_merge($divOptions, $div);
            }
            if (!isset($divOptions['tag'])) {
                $divOptions['tag'] = 'div';
            }
        }

        $label = null;
        if (isset($options['label'])) {
            $label = $options['label'];
            unset($options['label']);
        }

        $alt = null;
        if ($label !== false) {
            $alt = $label;
        }
        if (isset($options['alt'])) {
            $alt = $options['alt'];
            unset($options['alt']);
        }
		if ($alt === null) {
			if (strpos($fieldName, '.') !== false) {
				$alt = array_pop(explode('.', $fieldName));
			} else {
				$alt = $fieldName;
			}
			if (substr($alt, -3) == '_id') {
				$alt = substr($alt, 0, strlen($alt) - 3);
			}
			$alt = Inflector::humanize(Inflector::underscore($alt));
		}
        $class = null;
        if (isset($options['class'])) {
            $class = $options['class'];
            unset($options['class']);
        }

        if ($label !== false) {
            $label = $this->Form->_inputLabel($fieldName, $label, $options);
        }

        $out = array_merge(
            array('before' => null, 'label' => null, 'between' => null, 'input' => null, 'after' => null, 'error' => null),
            array('before' => $options['before'], 'label' => $label, 'between' => $options['between'], 'after' => $options['after'])
        );
        $format = null;
        if (is_array($options['format']) && in_array('input', $options['format'])) {
            $format = $options['format'];
        }
        unset($options['before'], $options['between'], $options['after'], $options['format']);

        $input = '';
        //if default is set to true, use it
        if ( $options['default'] !== FALSE ) {
            $file_url = ($options['full'] ? FULL_BASE_URL : '').$this->webroot.'default/'.str_replace('\\', '/', $options['default']).'/'.'default.png';
            $file_path = WWW_ROOT.'default/'.$options['default'].DS.'default.png';
            //check the thumbnail
            if ( $options['thumbsize'] != 'normal' ) {
                $file_url = ($options['full'] ? FULL_BASE_URL : '').$this->webroot.'default/'.str_replace('\\', '/', $options['default']).'/'.'thumb.'.$options['thumbsize'].'.'.'default.png';
                $file_path = WWW_ROOT.'default/'.$options['default'].DS.'thumb.'.$options['thumbsize'].'.'.'default.png';
            }
            if ( is_readable($file_path) ) {
                $input = $this->Html->image($file_url, array('alt' => $alt, 'class' => $class));
                if ( $options['thumbsize_link'] ){
                    $link_file_url = ($options['full'] ? FULL_BASE_URL : '').$this->webroot.'default/'.str_replace('\\', '/', $options['default']).'/'.'default.png';
                    $link_file_path = WWW_ROOT.'default/'.$options['default'].DS.'default.png';
                    if ( $options['thumbsize_link'] != 'normal' ) {
                        $link_file_url = ($options['full'] ? FULL_BASE_URL : '').$this->webroot.'default/'.str_replace('\\', '/', $options['default']).'/'.'thumb.'.$options['thumbsize_link'].'.'.'default.png';
                        $link_file_path = WWW_ROOT.'default/'.$options['default'].DS.'thumb.'.$options['thumbsize_link'].'.'.'default.png';
                    }
                    if (is_readable($link_file_path) ) {
                        //wrap image inside link to thumbsize_link file
                        $input = $this->Html->link($input, $link_file_url, array('escape' => false));
                    }
                }
            }
        }
        //if the file data available, override default if set by the block above
        if ( isset($fileData[$fieldName]) AND ! is_array($fileData[$fieldName]) AND $fileData[$fieldName] != '' ) {
            $is_image = substr($fileData[$options['fields']['mimetype']], 0, 5) == 'image';
            $file_url = ($options['full'] ? FULL_BASE_URL : '').$this->webroot.str_replace('\\', '/', $fileData[$options['fields']['dir']]).'/'.$fileData[$fieldName];
            $file_path = WWW_ROOT.$fileData[$options['fields']['dir']].DS.$fileData[$fieldName];
            //check the thumbnail
            if ( $options['thumbsize'] != 'normal' ) {
                $file_url = ($options['full'] ? FULL_BASE_URL : '').$this->webroot.str_replace('\\', '/', $fileData[$options['fields']['dir']]).'/'.'thumb.'.$options['thumbsize'].'.'.$fileData[$fieldName];
                $file_path = WWW_ROOT.$fileData[$options['fields']['dir']].DS.'thumb.'.$options['thumbsize'].'.'.$fileData[$fieldName];
            }
            if ( is_readable($file_path) ) {
                //the file is image
                if ( $is_image ) {
                    $input = $this->Html->image($file_url, array('alt' => $alt, 'class' => $class));
                    if ( $options['thumbsize_link'] ){
                        $link_file_url = ($options['full'] ? FULL_BASE_URL : '').$this->webroot.str_replace('\\', '/', $fileData[$options['fields']['dir']]).'/'.$fileData[$fieldName];
                        $link_file_path = WWW_ROOT.$fileData[$options['fields']['dir']].DS.$fileData[$fieldName];
                        if ( $options['thumbsize_link'] != 'normal' ) {
                            $link_file_url = ($options['full'] ? FULL_BASE_URL : '').$this->webroot.str_replace('\\', '/', $fileData[$options['fields']['dir']]).'/'.'thumb.'.$options['thumbsize_link'].'.'.$fileData[$fieldName];
                            $link_file_path = WWW_ROOT.$fileData[$options['fields']['dir']].DS.'thumb.'.$options['thumbsize_link'].'.'.$fileData[$fieldName];
                        }
                        if (is_readable($link_file_path) ) {
                            //wrap image inside link to thumbsize_link file
                            $input = $this->Html->link($input, $link_file_url, array('escape' => false));
                        }
                    }
                }
                //non-image file
                else {
                    $link_text = $options['show_realname'] ? $fileData[$options['fields']['realname']] : $options['link_text'];
                    $input = $this->Html->link($link_text, $file_url, array('class' => $class));
                }
            }
        }

        $out['input'] = $input;
        $format = $format ? $format : array('before', 'label', 'between', 'input', 'after', 'error');
        $output = '';
        if ( $out['input'] != '' ) {
            foreach ($format as $element) {
                $output .= $out[$element];
                unset($out[$element]);
            }

            if (!empty($divOptions['tag'])) {
                $tag = $divOptions['tag'];
                unset($divOptions['tag']);
                $output = $this->Html->tag($tag, $output, $divOptions);
            }
        }
        return $output;
    }

}
