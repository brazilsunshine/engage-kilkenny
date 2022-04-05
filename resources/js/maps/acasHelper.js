export const acasHelper = {
    /**
     * From feature.properties, return formatted string, object
     *
     * @param properties
     */
    getString (properties)
    {
        let str = "Area of Architectural Conservation: " + properties['Name'] + "<br>";

        str += "<br>This data has been sourced from <a href='https://www.kilkennycoco.ie/'>Kilkenny County Council</a>";

        return {
            str
        }
    }
}
