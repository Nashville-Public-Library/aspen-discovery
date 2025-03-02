{* This is a text-only email template; do not include HTML! *}
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Aspen Discovery//EN
CALSCALE:GREGORIAN
METHOD:PUBLISH
{foreach from=$instances item=instance}
BEGIN:VEVENT
SUMMARY:{if $instance->status}{$instance->status}: {/if}{$title|wordwrap:50:"\r\n  ":true}
UID:{$instance->uid}
TZID:{$timezone}
DTSTAMP:{$instance->date}
DTSTART:{$instance->date}
DURATION:PT{$hours}H{$minutes}M
LOCATION:{$location|wordwrap:50:"\r\n  ":true}{if $instance->sublocation} - {$instance->sublocation|wordwrap:50:"\r\n ":true}{/if}

DESCRIPTION:{$description|wordwrap:50:"\r\n  ":true}
X-ALT-DESC;FMTTYPE=text/html:{$htmlDescription|wordwrap:50:"\r\n  ":true}
END:VEVENT
{/foreach}
END:VCALENDAR
