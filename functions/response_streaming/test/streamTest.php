<?php
/**
 * Copyright 2023 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use PHPUnit\Framework\TestCase;

class streamTest extends TestCase
{
    public function testResponseStreaming()
    {
        if (!$projectId = getenv('GOOGLE_PROJECT_ID')) {
            $this->markTestSkipped('GOOGLE_PROJECT_ID must be set.');
        }

        $file = sys_get_temp_dir() . '/responsestreaming_test.php';
        $contents = file_get_contents(__DIR__ . '/../index.php');

        $contents = str_replace('YOUR_PROJECT_ID', $projectId, $contents);
        file_put_contents($file, $contents);

        ob_start();
        include $file;
        $result = ob_get_clean();

        $expected = sprintf('Got 6482 unique names in states: WA');
        $this->assertStringContainsString($expected, $result);
    }
}
