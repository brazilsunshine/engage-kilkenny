export const monumentsHelper = {
    /**
     * From feature.properties, return formatted string, object
     *
     * @param properties
     */
    getString (properties)
    {
        const keys = Object.keys(properties);

        let str = "";

        const changeName = {
            RMP_PROP: 'Record of Monuments & Places',
            CLASSDESC: 'Description',
            TLAND_NAME: 'Townland'
        };

        keys.forEach(key => {
            if (properties[key])
            {
                // We don't need to show these key value pairs
                if (
                    key !== 'ENTITY_ID'
                    && key !== 'CLASSCODE'
                    && key !== 'NAT_GRID_E'
                    && key !== 'NAT_GRID_N'
                    && key !== 'CO_ID'
                    && key !== 'ITM_E'
                    && key !== 'ITM_N'
                    && key !== 'SMRS'
                )
                {
                    // if RMP_PROP has a value of 1, we say "This is a Record of Monuments & Places"
                    const s = (properties[key] === 1)
                        ? "This is an official " + changeName[key]
                        : changeName[key] + ": " + properties[key];

                    str += s + " <br> ";
                }
            }
        });

        str += "<br>This data has been sourced by the <a href='https://maps.archaeology.ie/historicenvironment/'>National Inventory of Architectual Heritage (NIAH)</a>";

        return {
            str
        }
    }
}
