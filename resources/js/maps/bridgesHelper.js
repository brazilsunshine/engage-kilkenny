export const bridgesHelper = {
    /**
     * From feature.properties, return formatted string, object
     *
     * @param properties
     */
    getString (properties) {
        const keys = Object.keys(properties);

        let str = "";
        let bridge = {};

        str += "Name: " + properties['name'] + "<br>";

        keys.forEach(key => {
            if (properties[key])
            {
                bridge[key] = properties[key];

                if (key !== "full_id"
                    && key !== "osm_id"
                    && key !== "osm_type"
                    && key !== "layer"
                    && key !== "img"
                    && key !== "name"
                    && key !== "building"
                    && key !== "bridge")
                {
                    str += key + ": " + properties[key] + "<br>";
                }
            }
        });

        str += "<br>This data has been sourced from <a href='https://openstreetmap.org/'>OpenStreetMap</a>";

        return {
            str,
            bridge
        }
    }
}
