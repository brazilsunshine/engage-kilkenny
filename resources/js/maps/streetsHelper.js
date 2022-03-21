import L from "leaflet";
import {mapHelper} from "./mapHelpers";

export const streetsHelper = {
    /**
     * Helper function
     *
     * Get the colour for a street based on its highway/type
     *
     * @param streetType
     * @returns {string|null}
     */
    getStreetColour (streetType)
    {
        if (streetType)
        {
            if (streetType === "corridor")
            {
                return "#000000";
            }
            else if (streetType === "footway")
            {
                return "#95a5a6";
            }
            else if (streetType === "path")
            {
                return "#00a8ff";
            }
            else if (streetType === "pedestrian")
            {
                return "#2ecc71"
            }
            else if (streetType === "residential")
            {
                return "#abd123";
            }
            else if (streetType === "service")
            {
                return "#cb342b";
            }
            else if (streetType === "steps")
            {
                return "#95a5a6";
            }
            else if (streetType === "secondary")
            {
                return "#3498db";
            }
            else if (streetType === "tertiary")
            {
                return "#4f955b";
            }
            else if (streetType === "unclassified")
            {
                return "#9c88ff";
            }

            return null;
        }
    },

    /**
     * From feature.properties, return formatted string, object
     *
     * @param properties
     */
    getStringObject (properties)
    {
        const keys = Object.keys(properties);

        let street = {};
        let str = "";

        keys.forEach(key => {
            if (properties[key])
            {
                street[key] = properties[key];

                if (key !== 'osm_id' && key !== 'full_id' && key !== 'osm_type' && key !== "type")
                {
                    str += key + ": " + properties[key] + " <br> ";
                }
            }
        })

        return {
            str,
            street
        }
    },

    /**
     * Return a filtered array based on column name
     *
     * @param array
     * @param columnName
     * @return filteredStreetsArray
     */
    getFilteredArray (array, columnName)
    {
        return array.filter(feature => {
            if (feature.properties.hasOwnProperty(columnName)) {
                return feature;
            }
        });
    },

    /**
     * Using a filteredStreetsArray, get a matching value type
     *
     * get street.highway = corridor
     */
    getStreetByType (filteredArray, columnName, value)
    {
        return filteredArray.filter(feature => {
            if (feature.properties[columnName] === value) {
                return feature;
            }
        });
    }

};
