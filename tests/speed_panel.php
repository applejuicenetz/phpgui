<?php

declare(strict_types=1);

// Keep transfer panels on the same live speed snapshot as the topbar.
$source = file_get_contents(__DIR__ . '/../public/assets/js/components/common.js');
$panel = explode('export const ShareTabs', explode('export const SpeedPanel =', $source, 2)[1], 2)[0];
foreach ([
    "state.status[props.kind === 'ul' ? 'upload_speed' : 'download_speed'] ?? props.data.speed",
    'speed(currentSpeed)',
    'currentSpeed / data.max',
] as $contract) {
    if (!str_contains($panel, $contract)) {
        throw new RuntimeException('Speed panel must share topbar speed for label and progress: ' . $contract);
    }
}
if (str_contains($panel, 'speed(data.speed)') || str_contains($panel, 'data.speed / data.max')) {
    throw new RuntimeException('Speed panel must not display its independently polled speed');
}
echo "Speed panel: shared topbar snapshot, initial fallback and progress contracts passed\n";
