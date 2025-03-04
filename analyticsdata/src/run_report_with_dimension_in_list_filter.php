<?php
/**
 * Copyright 2022 Google LLC.
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

/**
 * Google Analytics Data API sample application demonstrating the usage of
 * dimension and metric filters in a report.
 * See https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/properties/runReport#body.request_body.FIELDS.dimension_filter
 * for more information.
 * Usage:
 *   composer update
 *   php run_report_with_dimension_in_list_filter.php YOUR-GA4-PROPERTY-ID
 */

namespace Google\Cloud\Samples\Analytics\Data;

// [START analyticsdata_run_report_with_dimension_in_list_filter]
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\MetricType;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\InListFilter;
use Google\Analytics\Data\V1beta\RunReportResponse;

/**
 * Runs a report using a dimension filter with `in_list_filter` expression.
 * The filter selects for when `eventName` is set to one of three event names
 * specified in the query.
 * This sample uses relative date range values. See
 * https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/DateRange
 * for more information.
 * @param string $propertyId Your GA-4 Property ID
 */
function run_report_with_dimension_in_list_filter(string $propertyId)
{
    // Create an instance of the Google Analytics Data API client library.
    $client = new BetaAnalyticsDataClient();

    // Make an API call.
    $response = $client->runReport([
        'property' => 'properties/' . $propertyId,
        'dimensions' => [new Dimension(['name' => 'eventName'])],
        'metrics' => [new Metric(['name' => 'sessions'])],
        'dateRanges' => [new DateRange([
                'start_date' => '7daysAgo',
                'end_date' => 'yesterday',
            ])
        ],
        'dimensionFilter' => new FilterExpression([
            'filter' => new Filter([
                'field_name' => 'eventName',
                'in_list_filter' => new InListFilter([
                    'values' => [
                        'purchase',
                        'in_app_purchase',
                        'app_store_subscription_renew',
                    ],
                ]),
            ]),
        ]),
    ]);

    printRunReportResponseWithDimensionInListFilter($response);
}

/**
 * Print results of a runReport call.
 * @param RunReportResponse $response
 */
function printRunReportResponseWithDimensionInListFilter(RunReportResponse $response)
{
    // [START analyticsdata_print_run_report_response_header]
    printf('%s rows received%s', $response->getRowCount(), PHP_EOL);
    foreach ($response->getDimensionHeaders() as $dimensionHeader) {
        printf('Dimension header name: %s%s', $dimensionHeader->getName(), PHP_EOL);
    }
    foreach ($response->getMetricHeaders() as $metricHeader) {
        printf(
            'Metric header name: %s (%s)' . PHP_EOL,
            $metricHeader->getName(),
            MetricType::name($metricHeader->getType())
        );
    }
    // [END analyticsdata_print_run_report_response_header]

    // [START analyticsdata_print_run_report_response_rows]
    print 'Report result: ' . PHP_EOL;

    foreach ($response->getRows() as $row) {
        printf(
            '%s %s' . PHP_EOL,
            $row->getDimensionValues()[0]->getValue(),
            $row->getMetricValues()[0]->getValue()
        );
    }
    // [END analyticsdata_print_run_report_response_rows]
}
// [END analyticsdata_run_report_with_dimension_in_list_filter]

// The following 2 lines are only needed to run the samples
require_once __DIR__ . '/../../testing/sample_helpers.php';
return \Google\Cloud\Samples\execute_sample(__FILE__, __NAMESPACE__, $argv);
