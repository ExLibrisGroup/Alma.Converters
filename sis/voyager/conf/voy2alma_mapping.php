<?php
// setting a default timezone
date_default_timezone_set('UTC');

// default dates when no date in SIF
// the offset will use php strtotime function
// which can translate any English textual datetime description into a timestamp
//
$default_patron_purge_date = getDefaultDateOffset('+10 years');
$default_patron_expiry_date = getDefaultDateOffset('+5 years');
$default_address_begin_date = getDefaultDateOffset('last month');
$default_address_end_date = getDefaultDateOffset('+5 years');


// Map these to something Alma understands via codetable
// .../almaws/v1/conf/code-tables/UserAddressTypes
$addressTypes = [
    '1' => 'primary',
    '2' => 'alternative',
    'DEFAULT' => 'ALL'
];



// An array mapping Voyager Barcode status to text
$barcodeStatusTypes = [
    '1' => 'ACTIVE',
    '2' => 'LOST',
    '3' => 'STOLEN',
    '4' => 'EXPIRED',
    '5' => 'OTHER',
    'DEFAULT' => 'INVALID',
    ' ' => 'INVALID'
];
// this should match one of the codes from the codetable for UserEmailTypes
// .../almaws/v1/conf/code-tables/UserEmailTypes
$primaryEmailType = "primary";
$defaultEmailType = "ALL";

// this should match one of the codes from the codetable for NoteTypes
// .../almaws/v1/conf/code-tables/NoteTypes
$userNoteType = "ALL";

// needs to be mapped to code from the codetable
// /almaws/v1/conf/code-tables/UserIdentifierTypes
$userIdentifierIDType = "BARCODE";

// status
// Status of user account. Possible codes are listed in 'Content Structure Status'
// [code table|https://developers.exlibrisgroup.com/blog/Working-with-the-code-tables-API].
// Default is ACTIVE.
$userStatus = "ACTIVE";
