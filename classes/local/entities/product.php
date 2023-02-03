<?php

namespace local_marketplace\local\entities;

use local_marketplace\local\exceptions\product_not_purchased;
use local_marketplace\local\exceptions\product_outofstock;
use local_marketplace\util\evocoin;
use moodle_url;

/**
 * Product entity class.
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class product extends base {
    const TYPE_DIGITAL = 1;
    const TYPE_DIGITAL_FILE = 2;
    const TYPE_PHYSICAL = 3;

    protected $table = 'marketplace_products';

    public function create($data) {
        global $DB, $CFG;

        try {
            $productid = $DB->insert_record($this->table, $data);

            $context = \context_system::instance();

            // Process attachments.
            $draftitemid = file_get_submitted_draft_itemid('images');
            file_save_draft_area_files($draftitemid, $context->id, 'local_marketplace', 'images', $productid, ['subdirs' => 0, 'maxfiles' => 10, 'accepted_types' => ['optimised_image']]);

            if ($data->type == self::TYPE_DIGITAL_FILE) {
                $draftitemid = file_get_submitted_draft_itemid('attachment');
                file_save_draft_area_files($draftitemid, $context->id, 'local_marketplace', 'attachment', $productid, ['subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => ['optimised_image']]);
            }

            return [true, get_string('createitem_success', 'local_marketplace')];
        } catch (\Exception $e) {
            if ($CFG->debugdisplay) {
                return [false, $e->getMessage()];
            }

            return [false, get_string('something_went_wrong', 'local_marketplace')];
        }
    }

    public function update($data) {
        global $DB, $CFG;

        try {
            $DB->update_record($this->table, $data);

            $context = \context_system::instance();

            // Process attachments.
            $draftitemid = file_get_submitted_draft_itemid('images');
            file_save_draft_area_files($draftitemid, $context->id, 'local_marketplace', 'images', $data->id, ['subdirs' => 0, 'maxfiles' => 10, 'accepted_types' => ['optimised_image']]);

            if ($data->type == self::TYPE_DIGITAL_FILE) {
                $draftitemid = file_get_submitted_draft_itemid('attachment');
                file_save_draft_area_files($draftitemid, $context->id, 'local_marketplace', 'attachment', $data->id, ['subdirs' => 0, 'maxfiles' => 1, 'accepted_types' => ['optimised_image']]);
            }

            return [true, get_string('updateitem_success', 'local_marketplace')];
        } catch (\Exception $e) {
            if ($CFG->debugdisplay) {
                return [false, $e->getMessage()];
            }

            return [false, get_string('something_went_wrong', 'local_marketplace')];
        }
    }

    public function delete($id) {
        global $DB, $CFG;

        try {
            $context = \context_system::instance();

            $DB->delete_records($this->table, ['id' => $id]);

            $this->delete_files($id, $context->id, 'images');

            $this->delete_files($id, $context->id, 'attachment');

            return [true, get_string('deleteitem_success', 'local_marketplace')];
        } catch (\Exception $e) {
            if ($CFG->debugdisplay) {
                return [false, $e->getMessage()];
            }

            return [false, get_string('something_went_wrong', 'local_marketplace')];
        }
    }

    public function has_children($id) {
        global $DB;

        if ($DB->count_records('marketplace_orders', ['productid' => $id])) {
            return true;
        }

        return false;
    }

    public function get_types() {
        return [
            self::TYPE_DIGITAL => get_string('type_digital', 'local_marketplace'),
            self::TYPE_DIGITAL_FILE => get_string('type_digital_file', 'local_marketplace'),
            self::TYPE_PHYSICAL => get_string('type_physical', 'local_marketplace')
        ];
    }

    protected function delete_files($id, $contextid, $filearea) {
        $fs = get_file_storage();

        $files = $fs->get_area_files($contextid, 'local_marketplace', $filearea, $id, 'timemodified', false);

        if ($files) {
            foreach ($files as $file) {
                $file->delete();
            }
        }
    }

    public function get_all() {
        global $DB;

        $sql = 'SELECT p.*, c.shortname as coursename
                FROM {marketplace_products} p
                LEFT JOIN {course} c ON c.id = p.courseid
                ORDER BY p.id DESC';

        $records = $DB->get_records_sql($sql);

        if (!$records) {
            return [];
        }

        foreach ($records as $record) {
            $record->images = $this->get_images($record->id);
            $record->instock = $this->instock($record);
        }

        return array_values($records);
    }

    public function get_site_and_course_products($courseid) {
        global $DB;

        $sql = 'SELECT p.*, c.shortname as coursename
                FROM {marketplace_products} p
                LEFT JOIN {course} c ON c.id = p.courseid
                WHERE courseid is null OR courseid = :courseid
                ORDER BY p.id DESC';

        $records = $DB->get_records_sql($sql, ['courseid' => $courseid]);

        if (!$records) {
            return [];
        }

        foreach ($records as $record) {
            $record->images = $this->get_images($record->id);
            $record->instock = $this->instock($record);
        }

        return array_values($records);
    }

    public function get_all_course_products($courseid) {
        global $DB;

        $sql = 'SELECT p.*, c.shortname as coursename
                FROM {marketplace_products} p
                INNER JOIN {course} c ON c.id = p.courseid
                WHERE courseid is null OR courseid = :courseid
                ORDER BY p.id DESC';

        $records = $DB->get_records_sql($sql, ['courseid' => $courseid]);

        if (!$records) {
            return [];
        }

        foreach ($records as $record) {
            $record->images = $this->get_images($record->id);
            $record->instock = $this->instock($record);
        }

        return array_values($records);
    }

    public function instock($product) {
        return $product->stock === null || $product->stock > 0;
    }

    public function decrease_stock($product, $quantity) {
        global $DB;

        if ($product->stock == null) {
            return true;
        }

        $product->stock -= $quantity;

        return $DB->update_record($this->table, $product);
    }

    public function get_images($id) {
        $context = \context_system::instance();

        $fs = get_file_storage();

        $files = $fs->get_area_files($context->id,
            'local_marketplace',
            'images',
            $id,
            'timemodified',
            false);

        if (!$files) {
            return false;
        }

        $images = [];

        foreach ($files as $file) {
            $path = [
                '',
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $id . $file->get_filepath() . $file->get_filename()
            ];

            $fileurl = \moodle_url::make_file_url('/pluginfile.php', implode('/', $path), true);

            $images[] = [
                'filename' => $file->get_filename(),
                'fileurl' => $fileurl->out(),
                'active' => false
            ];
        }

        $images[0]['active'] = true;

        return $images;
    }

    public function get_downloadable_file_url($productid) {
        $context = \context_system::instance();

        $fs = get_file_storage();

        $files = $fs->get_area_files($context->id,
            'local_marketplace',
            'attachment',
            $productid,
            'timemodified',
            false);

        if (!$files) {
            return false;
        }

        foreach ($files as $file) {
            $path = [
                '',
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $productid . $file->get_filepath() . $file->get_filename()
            ];

            return \moodle_url::make_file_url('/pluginfile.php', implode('/', $path), true);
        }

        return false;
    }
}
