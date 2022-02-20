/**
 * Helper function
 *
 * If the building has a NEWDATE (year of construction),
 *
 * Return a string hex colour if it exists.
 *
 * Otherwise, return null
 */
function getBuildingsColour (buildingsDate)
{
    if (buildingsDate)
    {
        if (buildingsDate >= 1200 && buildingsDate <= 1300)
        {
            return '#ffc403';
        }
        else if (buildingsDate >= 1301 && buildingsDate <= 1650)
        {
            return '#f00';
        }
        else if (buildingsDate >= 1651 && buildingsDate <= 1765)
        {
            return '#ff403f';
        }
        else if (buildingsDate >= 1766 && buildingsDate <= 1790)
        {
            return '#ff8080';
        }
        else if (buildingsDate >= 1791 && buildingsDate <= 1950)
        {
            return '#ff8080';
        }
    }

    return null;
}
