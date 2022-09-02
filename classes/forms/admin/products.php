<?php

namespace local_marketplace\forms\admin;

use local_marketplace\local\entities\category;
use local_marketplace\local\entities\product;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Products form.
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class products extends \moodleform {
    /**
     * The form definition.
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function definition() {
        $mform = $this->_form;

        $categoryentity = new category();
        $productentity = new product();

        $mform->addElement('select', 'categoryid', get_string('category', 'local_marketplace'), $categoryentity->get_all_select());
        $mform->addRule('categoryid', get_string('required'), 'required', null, 'client');
        $mform->addRule('categoryid', get_string('required'), 'nonzero', null, 'client');
        $mform->setType('categoryid', PARAM_TEXT);
        if (isset($this->_customdata->categoryid)) {
            $mform->setDefault('categoryid', $this->_customdata->categoryid);
        }

        $mform->addElement('text', 'name', get_string('name', 'local_marketplace'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->setType('name', PARAM_TEXT);
        if (isset($this->_customdata->name)) {
            $mform->setDefault('name', $this->_customdata->name);
        }

        $mform->addElement('textarea', 'description', get_string('description', 'local_marketplace'), ['rows' => 5]);
        $mform->addRule('description', get_string('required'), 'required', null, 'client');
        $mform->setType('description', PARAM_TEXT);
        if (isset($this->_customdata->description)) {
            $mform->setDefault('description', $this->_customdata->description);
        }

        $mform->addElement('filemanager', 'images', get_string('images', 'local_marketplace'), null,
            ['subdirs' => 0, 'maxfiles' => 10, 'accepted_types' => ['optimised_image']]);
        $mform->addRule('images', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'price', get_string('price', 'local_marketplace'));
        $mform->addRule('price', get_string('required'), 'required', null, 'client');
        $mform->addRule('price', get_string('onlynumbers', 'local_marketplace'), 'numeric', null, 'client');
        $mform->addRule('price', get_string('onlyintegers', 'local_marketplace'), 'nopunctuation', null, 'client');
        $mform->setType('price', PARAM_INT);
        if (isset($this->_customdata->price)) {
            $mform->setDefault('price', $this->_customdata->price);
        }

        $mform->addElement('select', 'type', get_string('product_type', 'local_marketplace'), $productentity->get_types());
        $mform->addRule('type', get_string('required'), 'required', null, 'client');
        $mform->addRule('type', get_string('required'), 'nonzero', null, 'client');
        $mform->setType('type', PARAM_TEXT);
        if (isset($this->_customdata->type)) {
            $mform->setDefault('type', $this->_customdata->type);
        }

        $mform->addElement('filemanager', 'attachment', get_string('attachment', 'local_marketplace'), null,
            ['subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => ['document', 'presentation', 'optimised_image']]);
        $mform->hideIf('attachment', 'type', 'neq', '2');

        $mform->addElement('text', 'stock', get_string('stock', 'local_marketplace'));
        $mform->addRule('stock', get_string('onlynumbers', 'local_marketplace'), 'numeric', null, 'client');
        $mform->addRule('stock', get_string('onlyintegers', 'local_marketplace'), 'nopunctuation', null, 'client');
        $mform->setType('stock', PARAM_INT);
        if (isset($this->_customdata->stock)) {
            $mform->setDefault('stock', $this->_customdata->stock);
        }

        $this->add_action_buttons(true);
    }

    public function definition_after_data() {
        $mform = $this->_form;

        if (isset($this->_customdata->id)) {
            $productentity = new product();

            $product = $productentity->find($this->_customdata->id, '*', MUST_EXIST);

            $context = \context_system::instance();

            $draftitemid = file_get_submitted_draft_itemid('images');
            file_prepare_draft_area($draftitemid, $context->id, 'local_marketplace', 'images', $product->id, ['subdirs' => 0, 'maxfiles' => 10, 'accepted_types' => ['optimised_image']]);
            $mform->getElement('images')->setValue($draftitemid);

            if ($product->type == $productentity::TYPE_DIGITAL_FILE) {
                $draftitemid = file_get_submitted_draft_itemid('attachment');
                file_prepare_draft_area($draftitemid, $context->id, 'local_marketplace', 'attachment', $product->id, ['subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => ['document', 'presentation', 'optimised_image']]);
                $mform->getElement('attachment')->setValue($draftitemid);
            }
        }
    }
}