export const wallsHelper = {

    getWallsString (properties) {

        const keys = Object.keys(properties);

        let str = "";

        keys.forEach(key => {

            if (properties[key] && key !== 'fid' && key !== 'full_id' && key !== 'osm_id' && key !== 'osm_type')
            {
                str += key + ": " + properties[key] + " <br> ";
            }
        });

        return str;
    }
}
