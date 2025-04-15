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

namespace mod_webpart;

/**
 * Class webpart
 *
 * @package    mod_webpart
 * @copyright  2025 Andrew Rowatt <A.J.Rowatt@massey.ac.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class webpart {
    public static function encode_html(object $data) :string {
        $html = '';
        $content = '';
        $spacing = "mt-$data->spacingbefore mb-$data->spacingafter";

        switch ($data->contenttype) {
            case 'heading':
                $content = "<$data->headinglevel class=\"mb-0\" style=\"overflow: hidden;\">$data->heading</$data->headinglevel>";
                break;
            case 'divider':
                $content = "<hr class=\"sl-$data->dividerstyle mt-0 mb-0\"/>";
                break;
            case 'spacer':
                $spacing = 'mt-0 mb-6';
                break;
        }

        $html = "<div class=\"$spacing\">$content</div>";

        return $html;
    }

    public static function decode_html(array &$data) :array {
        $html = $data['intro'];

        /* Setup defaults - these should match the UI defaults */
        $data['contenttype'] = 'heading';
        $data['heading'] = '';
        $data['headinglevel'] = 'h3';
        $data['spacingbefore'] = 2;
        $data['spacingafter'] = 2;
        $data['dividerstyle'] = 'theme1';
        
        $matches = [];
        /* Get spacing infomation */
        $result = preg_match('/<div class="mt-(\d) mb-(\d)">/', $html, $matches);
        if($result) {
            $data['spacingbefore'] = $matches[1];
            $data['spacingafter'] = $matches[2];
        }

        /* Check for a heading */
        $matches = [];
        $result = preg_match('/<(h\d).*?>(.*?)<\/h\d>/', $html, $matches);
        if($result === 1) {
            $data['heading'] = $matches[2];
            $data['headinglevel'] = $matches[1];
        } else {
            /* Check for divider */
            $result = preg_match('/<hr class="sl-(.*?) mt-0 mb-0"\/>/', $html, $matches);
            if($result === 1) {
                $data['contenttype'] = 'divider';
                $data['dividerstyle'] = $matches[1];
            } else {
                /* No heading or divider - so default to spacer */
                $data['contenttype'] = 'spacer';
            }
        }

        return $data;
    }
}
