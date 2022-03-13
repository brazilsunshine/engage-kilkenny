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
                return "#2ecc71";
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
                return "#ccc";
            }

            return null;
        }
    },

    // /**
    //  * On each street feature
    //  */
    // onEachStreet (feature, layer)
    // {
    //     layer.on('click', function (e)
    //     {
    //         const keys = Object.keys(feature.properties);
    //
    //         let street = {};
    //         let str = "";
    //
    //         keys.forEach(key => {
    //             if (feature.properties[key])
    //             {
    //                 street[key] = feature.properties[key];
    //
    //                 str += key + ": " + feature.properties[key] + " <br> ";
    //             }
    //         })
    //
    //         console.log({ street });
    //
    //         window.buildingsMap.street = street;
    //
    //         L.popup(mapHelper.popupOptions)
    //             .setLatLng(e.latlng)
    //             .setContent(str)
    //             .openOn(map);
    //
    //         L.DomEvent.stopPropagation(e);
    //     });
    //
    //     layer.on("mouseover", function(e) {
    //         layer.setStyle({
    //             fillOpacity: 0.4,
    //             color: 'yellow'
    //         });
    //     });
    //
    //     layer.on("mouseout",function(e) {
    //         layer.setStyle({
    //             fillOpacity: 0,
    //             color: '#3388ff'
    //         });
    //     });
    // }

    /**
     *
     */
    onEachStreetType (feature, layer)
    {
        const colour = streetsHelper.getStreetColour(feature.properties.highway);

        if (colour)
        {
            layer.setStyle({
                fillOpacity: 0.5,
                color: colour
            });
        }

        layer.on('click', function (e)
        {
            const keys = Object.keys(feature.properties);

            let street = {};
            let str = "";

            keys.forEach(key => {
                if (feature.properties[key])
                {
                    street[key] = feature.properties[key];

                    str += key + ": " + feature.properties[key] + " <br> ";
                }
            })

            console.log({ street });

            window.buildingsMap.street = street;

            L.popup(mapHelper.popupOptions)
                .setLatLng(e.latlng)
                .setContent(str)
                .openOn(map);

            L.DomEvent.stopPropagation(e);
        });
        layer.on("mouseover", function(e) {
            layer.setStyle({
                fillOpacity: 0.4,
                color: 'yellow'
            });
        });

        layer.on("mouseout",function(e) {

            const colour = streetsHelper.getStreetColour(feature.properties['highway']);

            if (colour)
            {
                layer.setStyle({
                    fillOpacity: 0.5,
                    color: colour
                });
            }

            layer.setStyle({
                fillOpacity: 0,
                color: colour
            });
        });
    },

    /**
     * On Each Street Material Layer
     */
    onEachStreetMaterial (feature, layer)
    {
        layer.on('click', function (e) {
            console.log(feature);
        });

        layer.on("mouseover", function(e) {
            layer.setStyle({
                fillOpacity: 0.4,
                color: 'yellow'
            });
        });

        layer.on("mouseout",function(e) {
            layer.setStyle({
                fillOpacity: 0,
                color: '#3388ff'
            });
        });
    },

    /**
     * Return a filtered streetsArray based on streetType
     *
     * @param streetsArray
     * @param streetType
     * @return filteredStreetsArray
     */
    getFilteredArray (streetsArray, streetType)
    {
        return streetsArray.filter(streetFeature => {
            if (streetFeature.properties.hasOwnProperty(streetType)) {
                return streetFeature;
            }
        });
    },

    /**
     * Using a filteredStreetsArray, get a matching value type
     *
     * get street.highway = corridor
     */
    getStreetByType (filteredStreetsArray, columnName, streetType)
    {
        return filteredStreetsArray.filter(feature => {
            if (feature.properties[columnName] === streetType) {
                return feature;
            }
        });
    }

};
