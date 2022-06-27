<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Segment\Model\Data\Segment;

use Magento\Segment\Model\Data\Segment;

class DataProvider
{

    public function getSegmentsData(): array
    {
        return [
            [
                Segment::ID_FIELD_NAME => 'd65eadf9-de5a-4dd7-a766-47b73fddabd1',
                'name' => 'Frequent content reader',
                'type' => 'Edge'
            ],
            [
                Segment::ID_FIELD_NAME => '8e7f913e-0d28-42b4-9c8a-457644589b7c',
                'name' => 'New shopper',
                'type' => 'Streaming'
            ],
            [
                Segment::ID_FIELD_NAME => 'c8899326-3dcf-40b9-b6cb-03be0d5efb10',
                'name' => 'Apple users',
                'type' => 'Streaming'
            ],
            [
                Segment::ID_FIELD_NAME => '1d304995-13c6-4494-8519-fcd2aece562b',
                'name' => 'Top buyers',
                'type' => 'Streaming'
            ]
        ];
    }
}
