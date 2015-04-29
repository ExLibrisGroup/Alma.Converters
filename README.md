# Alma.Converters

This is a collection of converters for Alma integrations.

  - sis/voyager (This converter converts Voyager patron SIF records into Alma SIS V2 records)
   - **Installation**
     - In most cases the only requirement is to install php
   - **Configuration**
     - Edit the voy2alma_mapping.php to map data from the code tables into the SIS document creation
     - Should the Voyager SIF format ever change modify the values in voyager_sif_definition.php
   - **Usage** 
    ```sh
    Usage - This is a command line utility to convert a Voyager Patron SIF file
            into an Alma SIS V2 XML file
    valid parameters are -f -p -h
    -f      Filename to process
    -p      In addition to creating the SIS XML file
            also output the xml to the console in "pretty-print" format (for debugging)
            ( the script will limit pretty print to the 1st 10 patrons converted)
    -h      Display the help file
    Examples:
    SIF2SISV2.php -h
        Displays the helpfile
    SIF2SISV2.php -f /path/to/SIFFile.sif
        Converts SIFFile.sif to SIFFile.sif.sis.xml
    SIF2SISV2.php -f /path/to/SIFFile.sif -p
        Converts SIFFile.sif to SIFFile.sif.sis.xml and displays the xml to the console
    SIF2SISV2.php -f /path/to/SIFFile.sif -f /path/to/SIFFile2.sif
        Converts SIFFile.sif to SIFFile.sif.sis.xml
        Converts SIFFile.sif to SIFFile2.sif.sis.xml
```

- 
