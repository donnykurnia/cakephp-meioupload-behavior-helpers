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
    );

    function displayFile($fieldName, $options = array()) {
        $this->setEntity($fieldName);

        $options = array_merge(
            array('before' => null, 'between' => null, 'after' => null, 'format' => null),
            $this->Form->_inputDefaults,
            $this->__default_options,
            $options
        );

        $modelKey = $this->params['models'][0];
        if ( ! isset($options['data']) ) {
            $options['data'] = $this->data;
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
			$alt = __(Inflector::humanize(Inflector::underscore($alt)), true);
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
        if ( $options['data'][$modelKey][$fieldName] != '' ) {
            $is_image = substr($options['data'][$modelKey][$options['fields']['mimetype']], 0, 5) == 'image';
            $file_url = '/'.$options['data'][$modelKey][$options['fields']['dir']].'/'.$options['data'][$modelKey][$fieldName];
            $file_path = WWW_ROOT.$options['data'][$modelKey][$options['fields']['dir']].DS.$options['data'][$modelKey][$fieldName];
            //check the thumbnail
            if ( $options['thumbsize'] != 'normal' ) {
                $thumb_file_path = WWW_ROOT.$options['data'][$modelKey][$options['fields']['dir']].DS.'thumb.'.$options['thumbsize'].'.'.$options['data'][$modelKey][$fieldName];
                if (is_readable($thumb_file_path) ) {
                    $file_path = $thumb_file_path;
                    $file_url = '/'.$options['data'][$modelKey][$options['fields']['dir']].'/'.'thumb.'.$options['thumbsize'].'.'.$options['data'][$modelKey][$fieldName];
                }
            }
            if (is_readable($file_path) ) {
                if ( $is_image ) {
                    $input = $this->Html->image($file_url, array('alt' => $alt));
                }
                else {
                    $link_text = $options['show_realname'] ? $options['data'][$modelKey][$options['fields']['realname']] : __($options['link_text'], true);
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
