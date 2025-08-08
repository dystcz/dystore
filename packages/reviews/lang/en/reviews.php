<?php

return [
    'navigation' => [
        'group' => 'Reviews',
        'label' => 'Reviews',
    ],
    'model' => [
        'label' => 'Review',
        'plural_label' => 'Reviews',
    ],
    'fields' => [
        'title' => 'Title',
        'purchasable' => 'Purchasable',
        'user' => 'User',
        'rating' => 'Rating',
        'comment' => 'Comment',
        'status' => 'Status',
        'published_at' => 'Published At',
        'meta_data' => 'Meta Data',
        'meta_key' => 'Key',
        'meta_value' => 'Value',
        'type' => 'Type',
        'published' => 'Published',
        'created' => 'Created',
        'updated' => 'Updated',
    ],
    'rating' => [
        '1' => '1 Star',
        '2' => '2 Stars',
        '3' => '3 Stars',
        '4' => '4 Stars',
        '5' => '5 Stars',
    ],
    'filters' => [
        'status' => 'Status',
        'rating' => 'Rating',
        'user' => 'User',
        'deleted' => 'Deleted',
    ],
];