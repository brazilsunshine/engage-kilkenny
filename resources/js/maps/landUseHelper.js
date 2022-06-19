export const landUseHelper = {
    /**
     * Return an appropriate hex value for each colour
     */
    getColour (landuse)
    {
        if (landuse === 'brownfield') {
            return '#993206';
        }
        else if (landuse === 'cemetery') {
            return '#808080';
        }
        else if (landuse === 'commercial') {
            return '#f6e58d';
        }

        return '#3388ff';
    }
}
