<?php

namespace local_marketplace\forms\admin;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

class categories extends \moodleform {
    /**
     * The form definition.
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'name', get_string('name', 'local_marketplace'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->setType('name', PARAM_TEXT);
        if (isset($this->_customdata->name)) {
            $mform->setDefault('name', $this->_customdata->name);
        }

        $this->add_action_buttons(true);
    }

    /**
     * A bit of custom validation for this form
     *
     * @param array $data An assoc array of field=>value
     * @param array $files An array of files
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $name = isset($data['name']) ? $data['name'] : null;

        if ($this->is_submitted() && (empty($name) || strlen($name) < 3)) {
            $errors['name'] = get_string('validation:namelen', 'local_marketplace');
        }

        return $errors;
    }
}