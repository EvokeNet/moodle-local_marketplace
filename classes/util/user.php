<?php

/**
 * User util class
 *
 * @package     local_marketplace
 * @copyright   2022 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

namespace local_marketplace\util;

defined('MOODLE_INTERNAL') || die;

class user {
    public function get_user_avatar_or_image($user = null) {
        global $USER, $PAGE, $CFG, $DB;

        if (!$user) {
            $user = $USER;
        }

        if (!is_object($user)) {
            $user = $DB->get_record('user', ['id' => $user], '*', MUST_EXIST);
        }

        $useravatar = get_user_preferences('evokegame_avatarid', null, $user);

        if ($useravatar) {
            return $CFG->wwwroot . '/local/evokegame/pix/a' . $useravatar . '.svg';
        }

        $userpicture = new \user_picture($user);
        $userpicture->size = 1;

        return $userpicture->get_url($PAGE);
    }

    public function get_user_groups($courseid, $userid = null) {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $sql = "SELECT g.id, g.name
                FROM {groups} g
                JOIN {groups_members} gm ON gm.groupid = g.id
                WHERE gm.userid = :userid AND g.courseid = :courseid";

        $groups = $DB->get_records_sql($sql, ['courseid' => $courseid, 'userid' => $userid]);

        if (!$groups) {
            return false;
        }

        return array_values($groups);
    }

    public function get_user_groups_names($courseid, $userid = null) {
        $groups = $this->get_user_groups($courseid, $userid);

        if (!$groups) {
            return false;
        }

        $groupnames = [];

        foreach ($groups as $group) {
            $groupnames[] = $group->name;
        }

        return implode(', ', $groupnames);
    }
}
