<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Output rendering of learn_analytics block
 *
 * @package    blocks_learn_analytics
 * @copyright  2014 CLAMP
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/report/learn_analytics/lib.php');

/**
 * Rendering methods for the learn_analytics reports
 */
class block_learn_analytics_renderer extends plugin_renderer_base {

    public function user_risk_list($risks, $users) {
        global $COURSE;
        if (empty($risks)) {
            return '';
        }

        $output = '';
        $output .= html_writer::tag('span', get_string('subheading', 'block_learn_analytics'));

        arsort($risks);
        $count = 0;
        foreach ($risks as $userid => $risk) {
            if ($count >= 10) { // TODO: add a instance-level setting for this number.
                break; // Only display top 10 at risk.
            }
            if (!isset($users[$userid])) {
                continue; // User doesnt exist?
            }
            $url = new moodle_url('/report/learn_analytics/index.php', array('id' => $COURSE->id, 'userid' => $userid));
            $link = html_writer::link($url, "{$users[$userid]->firstname} {$users[$userid]->lastname}");
            $display_value = sprintf("%.0f%%", 100 * $risk);
            $level = report_learn_analytics_get_risk_level($risk);
            $output .= html_writer::start_tag('div', array('class' => 'risk-row risk-level-'.$level));
            $output .= html_writer::tag('div', null, array('class' => 'trafficlight'));
            $output .= "&nbsp;$link $display_value";
            $output .= html_writer::end_tag('div');
            $count++;
        }
        $output .= html_writer::empty_tag('br');
        $url = new moodle_url('/report/learn_analytics/index.php', array('id' => $COURSE->id));
        $output .= html_writer::start_tag('div', array('class' => 'coursereportlink'));
        $output .= html_writer::link($url, get_string('viewcoursereport', 'block_learn_analytics'));
        $output .= html_writer::end_tag('div');

        return $output;
    }
}
