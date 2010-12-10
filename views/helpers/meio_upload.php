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
        'show_realname' => true,
        'show_filesize' => false,
        'link_text' => 'Download file',
        'type' => 'file',
        'full' => false,
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
        if ( isset($fileData[$fieldName]) AND ! is_array($fileData[$fieldName]) AND $fileData[$fieldName] != '' ) {
            $is_image = substr($fileData[$options['fields']['mimetype']], 0, 5) == 'image';
            $file_url = ($options['full'] ? FULL_BASE_URL : '').$this->webroot.str_replace('\\', '/', $fileData[$options['fields']['dir']]).'/'.$fileData[$fieldName];
            $file_path = WWW_ROOT.$fileData[$options['fields']['dir']].DS.$fileData[$fieldName];
            //check the thumbnail
            if ( $options['thumbsize'] != 'normal' ) {
                $thumb_file_path = WWW_ROOT.$fileData[$options['fields']['dir']].DS.'thumb.'.$options['thumbsize'].'.'.$fileData[$fieldName];
                if (is_readable($thumb_file_path) ) {
                    $file_path = $thumb_file_path;
                    $file_url = ($options['full'] ? FULL_BASE_URL : '').$this->webroot.$fileData[$options['fields']['dir']].'/'.'thumb.'.$options['thumbsize'].'.'.$fileData[$fieldName];
                }
            }
            if (is_readable($file_path) ) {
                if ( $is_image ) {
                    $input = $this->Html->image($file_url, array('alt' => $alt));
                }
                else {
                    $link_text = $options['show_realname'] ? $fileData[$options['fields']['realname']] : $options['link_text'];
                    $input = $this->Html->link($link_text, $file_url);
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
