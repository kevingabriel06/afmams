<?php
if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $timezone = 'Asia/Manila')
    {
        $tz = new DateTimeZone($timezone);
        $now = new DateTime('now', $tz);
        $ago = new DateTime($datetime, $tz);
        $diff = $now->diff($ago);

        if ($diff->days == 0) {
            if ($diff->h == 0) {
                if ($diff->i == 0) return "Just now";
                return $diff->i . " minutes ago";
            }
            return $diff->h . " hours ago";
        } elseif ($diff->days < 7) {
            return $diff->days . " days ago";
        } else {
            return $ago->format('F j, Y \a\t g:i a'); // Full date for older posts
        }
    }
}
