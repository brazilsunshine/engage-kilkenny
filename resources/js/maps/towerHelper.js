export const towerHelper = {
    /**
     * From feature.properties, return formatted string, object
     *
     * @param properties
     */
    getString (properties) {
        const keys = Object.keys(properties);

        let str = "";
        let tower = {};

        str += "Name: " + properties['name'] + "<br>";

        keys.forEach(key => {
            if (properties[key])
            {
                tower[key] = properties[key];

                if (key !== "full_id"
                    && key !== "osm_id"
                    && key !== "osm_type"
                    && key !== "layer"
                    && key !== "img"
                    && key !== "name"
                    && key !== "bridge")
                {
                    str += key + ": " + properties[key] + "<br>";
                }
            }
        });

        str += "<br>This data has been sourced from <a href='https://openstreetmap.org/'>OpenStreetMap</a>";

        return {
            str,
            tower
        }
    }
}
