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
 * Tests for Web part
 *
 * @package    mod_webpart
 * @category   test
 * @copyright  2025 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class webpart_test extends \advanced_testcase {
    // Write the tests here as public funcions.
    // Please refer to {@link https://docs.moodle.org/dev/PHPUnit} for more details on PHPUnit tests in Moodle.

    /**
     * Check that search terms are substituted with another given term when filtered.
     *
     * @dataProvider mod_webpart_encode_html_provider
     *
     * @covers ::webpart()
     */
    public function test_mod_webpart_encode_html(
            $contenttype,
            $heading,
            $headinglevel,
            $spacingbefore,
            $spacingafter,
            $dividerstyle, 
            $expected_html): void {
        $data = new \stdClass();
        $data->contenttype = $contenttype;
        $data->heading = $heading;
        $data->headinglevel = $headinglevel;
        $data->spacingbefore = $spacingbefore;
        $data->spacingafter = $spacingafter;
        $data->dividerstyle = $dividerstyle;

        $html = webpart::encode_html($data);

        $this->assertEquals($expected_html, $html);
    }

    public function mod_webpart_encode_html_provider(): array {
        return [
            'Heading' => [
                'contenttype' => 'heading',
                'heading' => 'Test Heading',
                'headinglevel' => 'h3',
                'spacingbefore' => 2,
                'spacingafter' => 2,
                'dividerstyle' => 'theme1',
                'expected_html' => '<div class="mt-2 mb-2"><h3 class="mb-0" style="overflow: hidden;">Test Heading</h3></div>',
            ],
            'Divider' => [
                'contenttype' => 'divider',
                'heading' => '',
                'headinglevel' => '',
                'spacingbefore' => 1,
                'spacingafter' => 1,
                'dividerstyle' => 'theme1',
                'expected_html' => '<div class="mt-1 mb-1"><hr class="sl-theme1 mt-0 mb-0"/></div>',
            ],
            'Spacer' => [
                'contenttype' => 'spacer',
                'heading' => '',
                'headinglevel' => '',
                'spacingbefore' => 0,
                'spacingafter' => 4,
                'dividerstyle' => '',
                'expected_html' => '<div class="mt-0 mb-'. webpart::DEFAULT_SPACER_SPACING . '"></div>',
            ]
        ];
    }

    /**
     * Check that search terms are substituted with another given term when filtered.
     *
     * @dataProvider mod_webpart_decode_html_provider
     *
     * @covers ::webpart()
     */
    public function test_mod_webpart_decode_html(
        $html,
        $expected_contenttype,
        $expected_heading,
        $expected_headinglevel,
        $expected_spacingbefore,
        $expected_spacingafter,
        $expected_dividerstyle): void {
    
        $data['intro'] = $html;

        $result = webpart::decode_html($data);
        $this->assertEquals($result['contenttype'], $expected_contenttype);
        $this->assertEquals($result['heading'], $expected_heading);
        $this->assertEquals($result['headinglevel'], $expected_headinglevel);
        $this->assertEquals($result['spacingbefore'], $expected_spacingbefore);
        $this->assertEquals($result['spacingafter'], $expected_spacingafter);
        $this->assertEquals($result['dividerstyle'], $expected_dividerstyle);
    }
 

public function mod_webpart_decode_html_provider(): array {
    return [
        'Heading' => [
            'html' => '<div class="mt-2 mb-2"><h3 class="mb-0" style="overflow: hidden;">Test Heading</h3></div>',
            'expected_contenttype' => 'heading',
            'expected_heading' => 'Test Heading',
            'expected_headinglevel' => 'h3',
            'expected_spacingbefore' => 2,
            'expected_spacingafter' => 2,
            'expected_dividerstyle' => 'theme1',
        ],
        'Divider' => [
            'html' => '<div class="mt-1 mb-1"><hr class="sl-theme1 mt-0 mb-0"/></div>',
            'expected_contenttype' => 'divider',
            'expected_heading' => webpart::DEFAULT_HEADING,
            'expected_headinglevel' => webpart::DEFAULT_HEADING_LEVEL,
            'expected_spacingbefore' => 1,
            'expected_spacingafter' => 1,
            'expected_dividerstyle' => 'theme1',
        ],
        'Spacer' => [
            'html' => '<div class="mt-0 mb-'. webpart::DEFAULT_SPACER_SPACING . '"></div>',
            'expected_contenttype' => 'spacer',
            'expected_heading' => webpart::DEFAULT_HEADING,
            'expected_headinglevel' => webpart::DEFAULT_HEADING_LEVEL,
            'expected_spacingbefore' => 0,
            'expected_spacingafter' => webpart::DEFAULT_SPACER_SPACING,
            'expected_dividerstyle' => webpart::DEFAULT_DIVIDER_STYLE,
        ]
    ];
}
}
