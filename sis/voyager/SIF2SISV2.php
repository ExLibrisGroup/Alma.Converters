<?php
include 'conf/voyager_sif_definition.php';
$codeMapping = 'conf/voy2alma_mapping.php';
include $codeMapping;

/**
 * @param $simpleXmlObject
 * @return string
 */
function prettyPrint(SimpleXMLElement $simpleXmlObject){

    if( ! is_object($simpleXmlObject) ){
        return "";
    }

    set_error_handler('HandleXmlError');
    //Format XML to save indented tree rather than one line
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($simpleXmlObject->asXML());
    restore_error_handler();

    return $dom->saveXML();
}

/**
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 * @return bool
 * @throws DOMException
 */
function HandleXmlError($errno, $errstr, $errfile, $errline)
{
    if ($errno==E_WARNING && (substr_count($errstr,"DOMDocument::loadXML()")>0))
    {
        print "Error in file " . $errfile . " at line " . $errline;
        throw new DOMException($errstr);
    }
    else
        return false;
}

/**
 * @param $line
 * @return bool
 */
function isValidSIF($line){
    global $baseRecordFieldsLength;
    global $addressRecordFieldsLength;

    $valid = false;

    if( $line !== "") {
        if (strlen($line) < $baseRecordFieldsLength+$addressRecordFieldsLength) {
            // record does not conform to minimum SIF record length
            print "Record does not have minimum SIF record length of 885. Length of string is " . strlen($line);
            print ".\n";
        } else {
            // record meets minimum length requirement
            $address_count = substr($line, $baseRecordFieldsLength, 1);
            if (($address_count < 1) or ($address_count > 9)) {
                // address count is not correct or wrong data type ie record string has address count at wrong offset
                print "Address count does not conform to rules on base SIF record. Returning null pointer...\n";
            } else {
                $valid = true;
            }
        }
    }
    return $valid;
}

/**
 * @param $fields
 * @param $data
 * @return array
 */
function pc_fixed_width_substr($fields,$data) {
    $r = [];
    $line_pos = 0;
    foreach($fields as $field_name => $field_length) {
        $fldVal = rtrim(substr($data,$line_pos,$field_length));
        $fldVal = iconv('ISO-8859-1','UTF-8',$fldVal);
        $r[$field_name] = $fldVal;
        $line_pos += $field_length;
    }
    return [$r,$line_pos];
}

/**
 * @param $string
 * @return mixed
 */
function read_sif($string) {
    global $baseRecordFields;
    $patronRecord = pc_fixed_width_substr($baseRecordFields,$string);
    $patronRecord = getAddresses($patronRecord,$string);
    $patronRecord = getPatronNotes($patronRecord,$string);
    return $patronRecord[0];
}

/**
 * @param $baseRecord
 * @param $string
 * @return array
 */
function getAddresses($baseRecord,$string)
{
    global $addressRecordFields;
    $address_count = substr($string, $baseRecord[1], 1);
    $line_pos = $baseRecord[1] + 1;

    for($i=1;$i<=$address_count;$i++) {
        foreach ($addressRecordFields as $field_name => $field_length) {
            $fldVal = rtrim(substr($string, $line_pos, $field_length));
            $fldVal = iconv('ISO-8859-1','UTF-8',$fldVal);
            $baseRecord[0]['addresses'][$i][$field_name] = $fldVal;
            $line_pos += $field_length;
        }
    }
    return [$baseRecord[0],$line_pos];
}

function getPatronNotes($baseRecord,$string) {
    $line_pos = $baseRecord[1];
    if ($line_pos < strlen($string)) {
        $fldVal = rtrim(substr($string, $line_pos, 1000));
        $fldVal = iconv('ISO-8859-1','UTF-8',$fldVal);
        $baseRecord[0]['note']=$fldVal;
        $line_pos += strlen($fldVal);
    }
    return [$baseRecord[0],$line_pos];
}

/**
 * @param $patronRec
 * @param $users
 */
function convertToAlmaSisXml($patronRec,SimpleXMLElement $users) {
    global $default_patron_purge_date;
    global $default_patron_expiry_date;
    global $userNoteType;

    // TODO: create a sep XML doc and only add it to $users if there are no errors along the way
    // this way we can reject entries

    // create a new user doc
    $user = $users->addChild('user');

    // record_type
    // On SIS load, this field is determined according to the SIS profile.

    // primary_id
    // For new users in SIS load
    // if not supplied, the system will generate a default based on the first and the last name
    $user->addChild('primary_id', $patronRec['patron_id']);

    // first_name
    // The user's first name
    $user->addChild('first_name', $patronRec['first_name']);

    // middle_name
    // the users middle name
    $user->addChild('middle_name', $patronRec['middle_name']);

    // last_name
    // the users last name
    $user->addChild('last_name', $patronRec['surname']);

    // pin_number
    // A four-digit number which serves as a password for the user to log on to the selfcheck machine (SIP2).
    // On SIS synch and PUT action, pin_number can be updated if it was not changed manually in Alma UI.
	// If the input payload for the PUT action contains this field empty, and there is an existing pin_number,
	// the existing value will be kept and will not be removed.
    // N/A in Voyager SIF

    // job_category
    // The types of jobs the user performs in the library, such as Cataloger, Circulation Desk Operator, and so forth.
    // Possible values are listed in 'Job Titles' [code table|https://developers.exlibrisgroup.com/blog/Working-with-the-code-tables-API].
    // On SIS synch and for external users in PUT action, this field will not be replaced if it was updated manually
    // (or if empty in the incoming user record).
    // N/A in Voyager SIF

    // job_description
    // General description of the user's job.
    // N/A in Voyager SIF

    // gender
    // The user's gender. Possible codes are listed in the 'Genders'
    // [code table|https://developers.exlibrisgroup.com/blog/Working-with-the-code-tables-API].
    // N/A in Voyager SIF

    // user_group
    // The group within the institution to which the user belongs.
    // Possible codes are listed in 'User Groups' [code table|https://developers.exlibrisgroup.com/blog/Working-with-the-code-tables-API].
    // Rules for user group usage are define in 'UserRecordTypeUserGroup' mapping table.
    // On SIS synch and for external users in PUT action, this field will not be replaced if it was updated manually
    // (or if empty in the incoming user record).
    // need to map

    // campus_code
    // The code of the campus related to the user.
    // Possible codes are listed in the "Campus List" of the general configuration menu.

    // web_site_url
    // The web site address related to the user.
    // N/A in Voyager SIF

    // cataloger_level
    // The cataloger level of the user. The cataloger level serves to control which catalogers
    // can edit and update records which have been edited and updated by other users.
    // N/A in Voyager SIF

    // preferred_language
    // The user's preferred language.
    // Possible codes are listed in 'User Preferred Language' [code table|https://developers.exlibrisgroup.com/blog/Working-with-the-code-tables-API].
	// Default is the institution language.
    // On SIS synch, this field will not be replaced if it was updated manually (or if empty in the incoming user record).
    // N/A in Voyager SIF

    // birth_date
    // The user's birth date
    // N/A in Voyager SIF

    // expiry_date
    // The estimated date when the user is expected to leave the institution.
    $user->addChild('expiry_date', almaDate($patronRec['patron_expiration_date'],$default_patron_expiry_date));

    // purge_date
    // the date on which the user is purged from the system.
    $user->addChild('purge_date', almaDate($patronRec['patron_purge_date'],$default_patron_purge_date));

    // account_type
    // On SIS load, users are always created as "External".

    // external_id
    // The external system from which the user was loaded into Alma. Relevant only for External users.
    // This field is mandatory during the POST and PUT actions for external users, and must match a valid SIS external system profile.
    // On SIS load, it is filled with the SIS profile code.

    // status
    // Status of user account. Possible codes are listed in 'Content Structure Status'
    // [code table|https://developers.exlibrisgroup.com/blog/Working-with-the-code-tables-API].
    // Default is ACTIVE.
    $user->addChild('status','ACTIVE');

    // contact_info
    // List of the user's contacts information
    buildUserContactInfo($user,$patronRec);

    // user_identifiers
    // List of the user's additional identifiers.
    $userIdentifiers  = $user->addChild('user_identifiers');
    for ($i = 1; $i<3; $i++){
        $pbc = $patronRec['patron_barcode_'.$i];
        if($pbc !== ''){
            $userIdentifier  = $userIdentifiers->addChild('user_identifier');
            // needs to be mapped to code from the codetable
            // /almaws/v1/conf/code-tables/UserIdentifierTypes
            $userIdentifier->addChild('id_type','BARCODE');
            $userIdentifier->addChild('value',$pbc);
            $userIdentifier->addChild('status',lookupBarcodeStatus($patronRec['barcode_status_'.$i]));
        }
        // need other logic for SSN and IID
        // hopefully not SSN
    }

    // user_blocks
    // List of the user's blocks.
    // N/A in Voyager SIF

    // user_notes
    // List of the user's related notes.
    if (strlen($patronRec['note'])){
        $userNotes = $user->addChild('user_notes');
        $userNote = $userNotes->addChild('user_note');
        $userNote->addChild('note_type',$userNoteType);
        $userNote->addChild('note_text',$patronRec['note']);
        $userNote->addChild('user_viewable','false');

    }

    // user_statistics
    // List of the user's related statistics.
    // these are different from voyager statistical categories
    // N/A in Voyager SIF

    // rs_libraries
    // List of the user's related resource sharing libraries.
    // N/A in Voyager SIF
}

/**
 * @param $offset
 * @return bool|string
 */
function getDefaultDateOffset($offset) {
    return date('Y-m-d', strtotime($offset));
}

/**
 * @param $barcodeStatus
 * @return string
 */
function lookupBarcodeStatus($barcodeStatus){
global $barcodeStatusTypes;
    $status = $barcodeStatusTypes[$barcodeStatus];
    return (is_null($status)) ? $barcodeStatusTypes["DEFAULT"] : $status;
}

/**
 * @param $addrType
 * @return mixed
 */
function lookupAddrType($addrType){
    global $addressTypes;
    $addressType = $addressTypes[$addrType];
    return (is_null($addressType)) ? $addrType["DEFAULT"] : $addressType;
}
/**
 * @param $date
 * @return mixed
 */
function almaDate ($date,$default){
    if(!strlen($date)){
        $date = $default;
    }
    // in order to convert to UTC
    // we need to convert this to a DateTime obj
    // and we cannot rely on system timezone
    // for now just pretend the date is already UTC
    return str_replace(".","-",$date)."Z";
}

/**
 * @param $user
 * @param $patronRec
 */
function buildUserContactInfo(SimpleXMLElement $user,$patronRec) {
    $contactInfo = $user->addChild('contact_info');
    buildUserAddresses($contactInfo,$patronRec);
    buildUserEmails($contactInfo,$patronRec);
    buildUserPhones($contactInfo,$patronRec);
}

/**
 * @param $contactInfo
 * @param $patronRec
 */
function buildUserAddresses(SimpleXMLElement $contactInfo,$patronRec) {
    global $default_address_begin_date;
    global $default_address_end_date;
    // List of user's addresses.
    $addresses = $contactInfo->addChild('addresses');
    $preferredSet = false;
    foreach ($patronRec['addresses'] as $patronAddress){
        if ($patronAddress['address_type'] == '1'){
            // Specific user's address
            $address = $addresses->addChild('address');

            if ($preferredSet == false){
                $address->addAttribute('preferred','true');
                $preferredSet = true;
            }

            $address->addChild('line1',$patronAddress['address_line_1']);
            $address->addChild('line2',$patronAddress['address_line_2']);
            $address->addChild('line3',$patronAddress['address_line_3']);
            $address->addChild('line4',$patronAddress['address_line_4']);
            $address->addChild('line5',$patronAddress['address_line_5']);

            $address->addChild('city',$patronAddress['city']);
            $address->addChild('state_province',$patronAddress['state_code']);
            $address->addChild('postal_code',$patronAddress['zipcode']);
            $address->addChild('country',$patronAddress['country']);
            $address->addChild('start_date',almaDate($patronAddress['address_begin_date'],$default_address_begin_date));
            $address->addChild('end_date',almaDate($patronAddress['address_end_date'],$default_address_end_date));

            // Address Types
            $addressTypes = $address->addChild('address_types');
            $addressTypes->addChild('address_type',lookupAddrType($patronAddress['address_type']));
        }
    }
}

/**
 * @param $contactInfo
 * @param $patronRec
 */
function buildUserEmails(SimpleXMLElement $contactInfo,$patronRec) {
    global $primaryEmailType;
    global $defaultEmailType;

    $emails = $contactInfo->addChild('emails');
    $preferredSet = false;
    $emailSet = false;

    foreach ($patronRec['addresses'] as $patronAddress){
        if ($patronAddress['address_type'] == '3'){
            // Specific user's address
            $email = $emails->addChild('email');
            $emailSet = true;
            if ($preferredSet == false){
                $email->addAttribute('preferred','true');
                $emailTypes = $email->addChild('email_types');
                $emailTypes->addChild('email_type',$primaryEmailType);
                $preferredSet = true;
            }
            $email->addChild('email_address',$patronAddress['address_line_1']);

            // email_types
            // Voyager has no concept of email type
            // so set a default type
            if ($preferredSet == false){
                $emailTypes = $email->addChild('email_types');
                $emailTypes->addChild('email_type',$defaultEmailType);
            }
        }
    }
    if ($emailSet===false) {
        // there are no emails set for this patron
        // ALMA SIS requires one so set a blank email
        $email = $emails->addChild('email');
        $email->addChild('email_address','');
        $emailTypes = $email->addChild('email_types');
        $emailTypes->addChild('email_type',$defaultEmailType);
    }
}

/**
 * @param $contactInfo
 * @param $patronRec
 */
function buildUserPhones(SimpleXMLElement $contactInfo,$patronRec) {
    // List of user's phone numbers
    // types come from the code table
    // .../almaws/v1/conf/code-tables/UserPhoneTypes
    $phones = $contactInfo->addChild('phones');

    foreach ($patronRec['addresses'] as $patronAddress) {
        addPhone($phones,'primary',preg_replace("/[^0-9,.]/", "",$patronAddress['phone_primary']));
        addPhone($phones,'mobile',preg_replace("/[^0-9,.]/", "",$patronAddress['phone_mobile']));
        addPhone($phones,'fax',preg_replace("/[^0-9,.]/", "",$patronAddress['phone_fax']));
        addPhone($phones,'general',preg_replace("/[^0-9,.]/", "",$patronAddress['phone_other']));
    }
}

/**
 * @param $phones
 * @param $type
 * @param $phoneNumber
 */
function addPhone(SimpleXMLElement $phones,$type,$phoneNumber) {
    // Specific user's phone number.
    if (strlen($phoneNumber)) {
        $phone = $phones->addChild('phone');
        $phone->addChild('phone_number',$phoneNumber);
        $phoneTypes = $phone->addChild('phone_types');
        $phoneTypes->addChild('phone_type',$type);
    }
}

/**
 ** MAIN PROGRAM
 **/

// TODO: move to getopts() so that different paramters can be passed in for configuration etc

//array_shift($argv);
//
//if (count($argv) == 0) {
//    $argv[0] = "php://stdin";
//}
$options = getopt("f:ph");

$showPrettyPrint = isset($options['p']);
$showHelp = isset($options['h']);

if (is_array($options["f"])){
    $files = $options["f"];
}
else{
    $files[] = $options["f"];
}

foreach ($files as $file) {

    // Create a new list of users
    $users = new SimpleXMLElement("<users></users>");
    $lineCount = 0;
    $lines = explode("\n", file_get_contents($file, "r"));
    foreach ($lines as $line) {

        if (isValidSIF($line)) {
            // For debug output of SIF line
            //echo($line);
            //echo("\n");
            $patronRec = read_sif($line);
            convertToAlmaSisXml($patronRec,$users);
            $lineCount++;
        }
    }
    // The total number of users. This can be used when
    // retrieving the users list using pagination.
    $users->addAttribute('total_record_count',$lineCount);
    $users->asXML($file.'.sis.xml');

    // Enable for debugging
    if ($showPrettyPrint) {
        echo prettyPrint($users);
    }
}
