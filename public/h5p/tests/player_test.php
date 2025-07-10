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
 * Testing the Moodle local class for managing the H5P Player.
 *
 * @package    core_h5p
 * @category   test
 * @copyright  2025 Gloria Chen <gloria.rouyu@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

use advanced_testcase;

/**
 *
 * Test class covering the player class.
 *
 * @package    core_h5p
 * @copyright  2025 Gloria Chen <gloria.rouyu@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @runTestsInSeparateProcesses
 */
final class player_test extends advanced_testcase {
    /**
     * Tests overriding the default text track to match the user's language preference.
     *
     * @covers ::override_default_text_track_to_user_language
     */
    public function test_override_default_text_track_to_user_language(): void {
        $this->setRunTestInSeparateProcess(true);
        $this->resetAfterTest(true);

        // Construct instance without calling constructor.
        $player = $this->getMockBuilder(player::class)
            ->disableOriginalConstructor()
            ->getMock();

        $params = new \stdClass();
        $params->interactiveVideo = new \stdClass();
        $params->interactiveVideo->video = new \stdClass();
        $params->interactiveVideo->video->textTracks = new \stdClass();

        // Two videoTrack entries with different language codes.
        $track1 = new \stdClass();
        $track1->srcLang = 'en';
        $track2 = new \stdClass();
        $track2->srcLang = 'fr';

        // Start with track2 first to test reorder.
        $params->interactiveVideo->video->textTracks->videoTrack = [$track2, $track1];

        $reflection = new \ReflectionClass($player);
        $method = $reflection->getMethod('override_default_text_track_to_user_language');
        $method->setAccessible(true);

        $result = $method->invoke($player, $params);

        // Assert the first item is the one with srcLang == 'en', sorted to the top.
        $this->assertEquals('en', $result->interactiveVideo->video->textTracks->videoTrack[0]->srcLang);
        $this->assertEquals('fr', $result->interactiveVideo->video->textTracks->videoTrack[1]->srcLang);

        // Also test that the method returns the same type.
        $this->assertInstanceOf(\stdClass::class, $result);
    }
}
