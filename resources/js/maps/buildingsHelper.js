import L from "leaflet";
import {mapHelper} from "./mapHelpers";

export const buildingsHelper = {

    /**
     * Helper function
     *
     * If the building has a NEWDATE (year of construction),
     *
     * Return a string hex colour if it exists.
     *
     * Otherwise, return null
     */
    getBuildingsColour (buildingsDate, hasStory = false)
    {
        if (buildingsDate)
        {
            if (buildingsDate >= 1200 && buildingsDate <= 1300) return '#fa6e6e';
            else if (buildingsDate >= 1301 && buildingsDate <= 1650) return '#e36386';
            else if (buildingsDate >= 1651 && buildingsDate <= 1765) return '#c16095';
            else if (buildingsDate >= 1766 && buildingsDate <= 1815) return '#9a609b';
            else if (buildingsDate >= 1816 && buildingsDate <= 1916) return '#715e96';
            else if (buildingsDate >= 1917 && buildingsDate <= 1950) return '#4d5a86';
            else if (buildingsDate >= 1951 && buildingsDate <= 2000) return '#345271';
            else if (buildingsDate >= 2001 && buildingsDate <= 2022) return '#2a4858';
        }
        else if (hasStory)
        {
            return '#2ecc71';
        }
        else
        {
            return '#3388ff';
        }
    },

    /**
     * From the feature.properties object,
     *
     * Extract the keys we need as a string and object
     *
     * @param properties
     * @return string, object
     */
    getStringBuildingObject (properties)
    {
        const keys = Object.keys(properties);

        const niahKeysToInclude = [
            'NAME',
            'NAME_2',
            'NUMBER',
            'STREET1',
            'STREET2',
            'TLAND_NAME',
            'REG_NO',
            'created_da',
            'last_edite',
            'last_edi_1',
            'VALUE_DESC', // regional, national ?
            'ORIGINALTY',
            'INUSETYPE', // interesting: list of features.
            'NIAH_SITE_' // this is a link
        ];

        const niahKeysToExlude = [
            'DATE_2', // A-B
            'PUBLISH',
            'SURVEY_ID',
            'created_us',
            'TOWN',
            'COUNTY_COD',
            'COUNTY',
            'LOCAL_AUTH',
            'RPS', // Need this - Record of Protected Structures
            'RMP' // Need this - Record of Momuments + Places
        ]

        let building = {};
        let str = "";

        // Check if the building has NIAH data first
        if (properties['REG_NO']) {

            str += "This data has been sourced from the <a href='https://maps.archaeology.ie/historicenvironment/' target='_blank'>National Inventory of Architectural Heritage</a>: <br> <br>";

            // First, Add Year of Construction to the popup
            if (properties['NEWDATE']) {
                str += "<p style='margin: 0 !important;'>When it was built: " + properties['NEWDATE'] + "</p>";
            }

            // Add key: value from NIAH. Do not include date again.
            niahKeysToInclude.forEach(key => {
                if (properties[key] && key !== 'NEWDATE') {
                    str += "<p style='margin: 0 !important;'>" + key + ": " + properties[key] + "</p>";
                }
            });

            str += "<hr style='background-color: #ccc; margin: 1em; height: 1px;'>";
        }

        // If the building object does not have REG_NO, the data is from OSM, for now. Will be our data soon.

        str += "This data has been sourced from <a href='https://openstreetmap.org' target='_blank'>OpenStreetMap</a>: <br> <br>";

        keys.forEach(key => {

            if (properties[key] && key !== 'NIAH_SITE_')
            {
                building[key] = properties[key];

                if (
                    key !== 'osm_id'
                    && key !== 'full_id'
                    && key !== 'osm_type'
                    && key !== 'type'
                    && key !== 'NEWDATE'
                    && !niahKeysToInclude.includes(key)
                    && !niahKeysToExlude.includes(key)
                )
                {
                    str += "<p style='margin: 0 !important;'>" + key + ": " + properties[key] + "</p>";
                }
            }
        });

        return { str, building };
    }
}

