<?php

return [
    /*
     * One unified posting API rather than a separate integration per network.
     * Five OAuth flows that each break independently is not a thing worth
     * owning for a blog. Blank endpoint means nothing is attempted.
     */
    'endpoint' => env('SOCIAL_ENDPOINT', ''),
    'key' => env('SOCIAL_KEY', ''),

    /*
     * X is deliberately absent. It now requires your own developer app, bills
     * per post containing a link, and dropped RSS auto-posting in March 2026.
     * Add it here if that ever stops being true.
     */
    'channels' => array_filter(explode(',', (string) env('SOCIAL_CHANNELS', 'linkedin,facebook,bluesky'))),
];
