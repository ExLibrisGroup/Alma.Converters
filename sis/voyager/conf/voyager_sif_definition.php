<?php
// base record definition
// when the SIF changes update the
// fixed field length for the given fields

// These definitions will work with Voyager versions 2000.0 to present

$baseRecordFields = [
    'patron_id' => 10,
    'patron_barcode_id_1' => 10,
    'patron_barcode_1' => 25,
    'patron_group_1' => 10,
    'barcode_status_1' => 1,
    'barcode_modified_date_1' => 10,
    'patron_barcode_id_2' => 10,
    'patron_barcode_2' => 25,
    'patron_group_2' => 10,
    'barcode_status_2' => 1,
    'barcode_modified_date_2' => 10,
    'patron_barcode_id_3' => 10,
    'patron_barcode_3' => 25,
    'patron_group_3' => 10,
    'barcode_status_3' => 1,
    'barcode_modified_date_3' => 10,
    'registration_date' => 10,
    'patron_expiration_date' => 10,
    'patron_purge_date' => 10,
    'voyager_date' => 10,
    'voyager_updated' => 10,
    'circulation_happening_location_code' => 10,
    'institution_id' => 30,
    'ssn' => 11,
    'statistical_category_1' => 3,
    'statistical_category_2' => 3,
    'statistical_category_3' => 3,
    'statistical_category_4' => 3,
    'statistical_category_5' => 3,
    'statistical_category_6' => 3,
    'statistical_category_7' => 3,
    'statistical_category_8' => 3,
    'statistical_category_9' => 3,
    'statistical_category_10' => 3,
    'name_type' => 1,
    'surname' => 30,
    'first_name' => 20,
    'middle_name' => 20,
    'title' => 10,
    'historical_charges' => 10,
    'claims_returned_count' => 5,
    'self_shelved_count' => 5,
    'lost_items_count' => 5,
    'late_media_returns' => 5,
    'historical_bookings' => 5,
    'canceled_bookings' => 5,
    'unclaimed_bookings' => 5,
    'historical_callslips' => 5,
    'historical_distributions' => 5,
    'historical_shortloans' => 5,
    'unclaimed_shortloans' => 5
];

// address record definition
// when the SIF changes update the
// fixed field length for the given fields
$addressRecordFields = [
    'address_id' => 10,
    'address_type' => 1,
    'address_status_code' => 1,
    'address_begin_date' => 10,
    'address_end_date' => 10,
    'address_line_1' => 50,
    'address_line_2' => 40,
    'address_line_3' => 40,
    'address_line_4' => 40,
    'address_line_5' => 40,
    'city' => 40,
    'state_code' => 7,
    'zipcode' => 10,
    'country' => 20,
    'phone_primary' => 25,
    'phone_mobile' => 25,
    'phone_fax' => 25,
    'phone_other' => 25,
    'date_added' => 10
];

// Since the minimum valid length for a patron must be base + 1 address
// sum up the values from the definitions above
// the code will use these values to determine if the min length requirement
// is met during validation dynamically
$baseRecordFieldsLength = array_sum($baseRecordFields);
$addressRecordFieldsLength = array_sum($addressRecordFields);
