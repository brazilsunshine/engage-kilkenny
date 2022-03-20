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

        let building = {};
        let str = "";

        keys.forEach(key => {

            if (properties[key] && key !== 'NIAH_SITE_')
            {
                building[key] = properties[key];

                if (key !== 'osm_id' && key !== 'full_id' && key !== 'osm_type' && key !== "type")
                {
                    if (key === 'NEWDATE')
                    {
                        str += "<p style='margin: 0 !important;'>Year of Construction: " + properties[key] + "</p>";
                    }
                    else
                    {
                        str += "<p style='margin: 0 !important;'>" + key + ": " + properties[key] + "</p>";
                    }

                }
            }
        });

        return { str, building };
    }

}

