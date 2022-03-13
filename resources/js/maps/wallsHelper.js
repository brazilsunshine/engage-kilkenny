export const wallsHelper = {

    getWallsString (properties) {

        const keys = Object.keys(properties);

        let str = "";

        keys.forEach(key => {

            if (properties[key])
            {
                str += key + ": " + properties[key] + " <br> ";
            }
        });

        return str;
    }
}
