<template>
    <div class="h100 flex">
        <!-- The map & data -->
        <div
            id="buildings-map"
            ref="buildings-map"
        />

        <!-- Right-hand form container -->
        <SideMapContainer
            :key="buildingsKey"
        />

        <!-- Websockets -->
        <LiveEvents />
    </div>
</template>

<script>
import LiveEvents from '../../components/LiveEvents';

import {
    CLUSTER_ZOOM_THRESHOLD,
    MAX_ZOOM,
    MEDIUM_CLUSTER_SIZE,
    LARGE_CLUSTER_SIZE,
    MIN_ZOOM,
    ZOOM_STEP
} from '../../constants';

import L from 'leaflet';
import './SmoothWheelZoom.js';

import { mapHelper } from '../../maps/mapHelpers';
import { buildingsHelper } from "../../maps/buildingsHelper";
import { streetsHelper } from "../../maps/streetsHelper";
import { wallsHelper } from "../../maps/wallsHelper";
import SideMapContainer from "../../components/global/SideMapContainer";

var map;

var buildings;
var buildingsWithoutYear;

var streets, streetTypes;
var corridor, footway, path, pedestrian, residential, secondary, service, steps, tertiary, unclassified;
var streetsMaterial;

var walls;

// todo - var parks, carParks, publicBuildings, stories;

var layerController;

/**
 * Create the Layer Controller used to toggle all layers
 *
 * This uses the Leaflet.Control.Layer.Tree plugin allowing us to nest layers.
 */
function createLayerController ()
{
    let overlayLayersTree = {
        label: 'All Data',
        children: [
            {
                label: 'Buildings',
                selectAllCheckbox: false,
                children: [
                    { label: ' Year Of Construction', layer: buildings },
                    { label: ' Year Missing', layer: buildingsWithoutYear }
                ]
            },
            {
                label: 'Streets',
                selectAllCheckbox: false,
                children: [
                    {
                        label: ' Type',
                        layer: streetTypes,
                        children: [
                            { label: ' Corridor', layer: corridor },
                            { label: ' Footway', layer: footway },
                            { label: ' Path', layer: path },
                            { label: ' Pedestrian', layer: pedestrian },
                            { label: ' Residential', layer: residential },
                            { label: ' Secondary', layer: secondary },
                            { label: ' Service', layer: service },
                            { label: ' Steps', layer: steps },
                            { label: ' Tertiary', layer: tertiary },
                            { label: ' Unclassified', layer: unclassified }
                        ],
                    },
                    {
                        label: ' Material',
                        layer: streetsMaterial
                    }
                ]
            },
            {
                label: 'Historic',
                selectAllCheckbox: false,
                children: [
                    {
                        label: ' Walls',
                        layer: walls
                    }
                ]
            }
        ]
    }

    layerController = L.control.layers.tree(null, overlayLayersTree).addTo(map);
}

// function onEachBuildingWithoutYear (feature, layer)
// {
//
// }

export default {
    name: 'BuildingsMap',
    components: {
        SideMapContainer,
        LiveEvents
    },
    data () {
        return {
            building: null,
            street: null,
            stories: [],
            buildingsKey: 0
        };
    },
    mounted () {
        /** 0: Bind variable outside of vue scope */
        window.buildingsMap = this;

        /** 1. Create map object */
        map = L.map('buildings-map', {
            center: [52.652046, -7.2501555],
            zoom: 15,
            scrollWheelZoom: false,
            smoothWheelZoom: true,
            smoothSensitivity: 1,
        });

        map.scrollWheelZoom = true;

        /** 2. Add tiles, attribution, set limits */
        const mapLink = '<a href="https://openstreetmap.org">OpenStreetMap</a>';

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; ' + mapLink + ' & Contributors',
            maxZoom: MAX_ZOOM,
            minZoom: MIN_ZOOM
        }).addTo(map);

        map.attributionControl.addAttribution(' Architecture Data &copy NIAH');

        // Add the layers
        addBuildingLayers(this.$store.state.globalmap.buildings.features);
        addStreetLayers(this.$store.state.globalmap.streets.features);
        addWallsLayer(this.$store.state.globalmap.walls.features);

        createLayerController();

        map.on('click', function (e) {
            window.buildingsMap.building = null;
            window.buildingsMap.street = null;
        });

        createLegends();
    }
};

/**
 * Use the array of buildings from the backend
 *
 * Create many layers that can be turned on and off
 */
function addBuildingLayers (buildingsArray)
{
    buildings = L.geoJSON(null, {
        onEachFeature: onEachBuilding
    }).addTo(map);

    const filteredBuildingsWithYear = buildingsArray.filter(feature => {
        return (feature.properties.hasOwnProperty('NEWDATE'));
    });

    buildings.addData({
        features: filteredBuildingsWithYear,
        type: "FeatureCollection"
    });

    buildingsWithoutYear = L.geoJSON(null, {
        onEachFeature: onEachBuilding
    });

    const filteredBuildingsWithoutYear = buildingsArray.filter(feature => {
        if (!feature.properties.hasOwnProperty('NEWDATE')) {
            return feature;
        }
    });

    buildingsWithoutYear.addData({
        features: filteredBuildingsWithoutYear,
        type: "FeatureCollection"
    });
}

/**
 * On each building feature
 */
function onEachBuilding (feature, layer)
{
    const colour = buildingsHelper.getBuildingsColour(feature.properties.NEWDATE);

    layer.setStyle({
        fillOpacity: 0.5,
        color: colour
    });

    layer.on('click', async function (e)
    {
        const { str, building } = buildingsHelper.getStringBuildingObject(feature.properties);

        window.buildingsMap.building = building;

        L.popup(mapHelper.popupOptions)
            .setLatLng(e.latlng)
            .setContent(str)
            .openOn(map);

        L.DomEvent.stopPropagation(e);

        // Check if the building has any stories
        await axios.get('/check-building', {
            params: {
                osm_id: building.osm_id
            }
        })
        .then(response => {
            console.log('check_building', response);

            if (response.data.success) {
                window.buildingsMap.stories = response.data.stories;
            }
        })
        .catch(error => {
            console.error('check_building', error);
        });

        window.buildingsMap.buildingsKey++;
    });

    layer.on("mouseover", function(e) {
        layer.setStyle({
            fillOpacity: 0.4,
            color: 'yellow'
        });
    });

    layer.on("mouseout",function(e) {
        const colour = buildingsHelper.getBuildingsColour(feature.properties.NEWDATE, feature.properties.hasStory);

        layer.setStyle({
            fillOpacity: 0.5,
            color: colour
        });
    });
}

/**
 * Use the array of streets from the backend
 *
 * Create many layers that can be turned on and off
 */
function addStreetLayers (streetsArray)
{
    // 1. Create empty L.geoJSON layers
    // All Types
    streetTypes = L.geoJSON(null, { onEachFeature: streetsHelper.onEachStreetType });
    // Each Filtered Type (10)
    corridor = L.geoJSON(null, { onEachFeature: streetsHelper.onEachStreetType });
    footway = L.geoJSON(null, { onEachFeature: streetsHelper.onEachStreetType });
    path = L.geoJSON(null, { onEachFeature: streetsHelper.onEachStreetType });
    pedestrian = L.geoJSON(null, { onEachFeature: streetsHelper.onEachStreetType });
    residential = L.geoJSON(null, { onEachFeature: streetsHelper.onEachStreetType });
    secondary = L.geoJSON(null, { onEachFeature: streetsHelper.onEachStreetType });
    service = L.geoJSON(null, { onEachFeature: streetsHelper.onEachStreetType });
    steps = L.geoJSON(null, { onEachFeature: streetsHelper.onEachStreetType });
    tertiary = L.geoJSON(null, { onEachFeature: streetsHelper.onEachStreetType });
    unclassified = L.geoJSON(null, { onEachFeature: streetsHelper.onEachStreetType });

    // 2. Create a filtered geojson array of each layer we want to use
    // Get array of streets of column name "highway"
    const filteredStreetsArray = streetsHelper.getFilteredArray(streetsArray, 'highway');
    // Using street.highway, create filteredArray for various highway values
    const corridorArray = streetsHelper.getStreetByType(filteredStreetsArray, 'highway', 'corridor');
    const footwayArray = streetsHelper.getStreetByType(filteredStreetsArray, 'highway', 'footway');
    const pathArray = streetsHelper.getStreetByType(filteredStreetsArray, 'highway', 'path');
    const pedestrianArray = streetsHelper.getStreetByType(filteredStreetsArray, 'highway', 'pedestrian');
    const residentialArray = streetsHelper.getStreetByType(filteredStreetsArray, 'highway', 'residential');
    const secondaryArray = streetsHelper.getStreetByType(filteredStreetsArray, 'highway', 'secondary');
    const serviceArray = streetsHelper.getStreetByType(filteredStreetsArray, 'highway', 'service');
    const stepsArray = streetsHelper.getStreetByType(filteredStreetsArray, 'highway', 'steps');
    const tertiaryArray = streetsHelper.getStreetByType(filteredStreetsArray, 'highway', 'tertiary');
    const unclassifiedArray = streetsHelper.getStreetByType(filteredStreetsArray, 'highway', 'unclassified');

    // 3.1 Add the filtered array to the empty geojson layer
    streetTypes.addData({ features: filteredStreetsArray, type: "FeatureCollection" });
    // 3.2 Add each filtered value type to the empty geojson layer
    corridor.addData({ features: corridorArray, type: "FeatureCollection" });
    footway.addData({ features: footwayArray, type: "FeatureCollection" });
    path.addData({ features: pathArray, type: "FeatureCollection" });
    pedestrian.addData({ features: pedestrianArray, type: "FeatureCollection" });
    residential.addData({ features: residentialArray, type: "FeatureCollection" });
    secondary.addData({ features: secondaryArray, type: "FeatureCollection" });
    service.addData({ features: serviceArray, type: "FeatureCollection" });
    steps.addData({ features: stepsArray, type: "FeatureCollection" });
    tertiary.addData({ features: tertiaryArray, type: "FeatureCollection" });
    unclassified.addData({ features: unclassifiedArray, type: "FeatureCollection" });
    // 3.3 Add the streetMaterial column type
    streetsMaterial = L.geoJSON(null, {
        onEachFeature: streetsHelper.onEachStreetMaterial
    });
    const filteredStreetsMaterial = streetsHelper.getFilteredArray(streetsArray, 'material');
    streetsMaterial.addData({ features: filteredStreetsMaterial, type: "FeatureCollection" });
}

function addWallsLayer (wallsArray)
{
    walls = L.geoJSON(null, {
        onEachFeature: onEachWalls
    }).addTo(map);

    walls.addData({ features: wallsArray, type: "FeatureCollection" });
}

/**
 * On each walls feature
 */
function onEachWalls (feature, layer)
{
    layer.on('click', async function (e) {

        const str = wallsHelper.getWallsString(feature.properties);

        L.popup(mapHelper.popupOptions)
            .setLatLng(e.latlng)
            .setContent(str)
            .openOn(map);

        L.DomEvent.stopPropagation(e);
    });
}

function createLegends ()
{
    // Buildings: Year of construction
    let legend = L.control({
        position: 'bottomleft'
    });

    legend.onAdd = function (map) {
        let legend = L.DomUtil.create('div', 'info legend');
        legend.innerHTML += '<i style="background:' + buildingsHelper.getBuildingsColour(1200) + '"></i> 1200 - 1300' + '<br>';
        legend.innerHTML += '<i style="background:' + buildingsHelper.getBuildingsColour(1301) + '"></i> 1301 - 1650' + '<br>';
        legend.innerHTML += '<i style="background:' + buildingsHelper.getBuildingsColour(1651) + '"></i> 1651 - 1765' + '<br>';
        legend.innerHTML += '<i style="background:' + buildingsHelper.getBuildingsColour(1790) + '"></i> 1766 - 1815' + '<br>';
        legend.innerHTML += '<i style="background:' + buildingsHelper.getBuildingsColour(1816) + '"></i> 1816 - 1916' + '<br>';
        legend.innerHTML += '<i style="background:' + buildingsHelper.getBuildingsColour(1816) + '"></i> 1917 - 1950' + '<br>';
        legend.innerHTML += '<i style="background:' + buildingsHelper.getBuildingsColour(1816) + '"></i> 1951 - 2000' + '<br>';
        legend.innerHTML += '<i style="background:' + buildingsHelper.getBuildingsColour(1816) + '"></i> 2001 - 2022' + '<br>';

        return legend;
    };
    legend.addTo(map);

    // Streets - Type of street (highway)
    let streetLegend = L.control({
        position: 'bottomleft'
    });
    streetLegend.onAdd = function (map) {
        let legend = L.DomUtil.create('div', 'info legend street-legend');

        legend.innerHTML += '<i style="background:' + streetsHelper.getStreetColour('corridor') + '"></i> Corridor <br>';
        legend.innerHTML += '<i style="background:' + streetsHelper.getStreetColour('footway') + '"></i> Footway <br>';
        legend.innerHTML += '<i style="background:' + streetsHelper.getStreetColour('path') + '"></i> Path <br>';
        legend.innerHTML += '<i style="background:' + streetsHelper.getStreetColour('pedestrian') + '"></i> Pedestrian <br>';
        legend.innerHTML += '<i style="background:' + streetsHelper.getStreetColour('residential') + '"></i> Residential <br>';
        legend.innerHTML += '<i style="background:' + streetsHelper.getStreetColour('secondary') + '"></i> Secondary <br>';
        legend.innerHTML += '<i style="background:' + streetsHelper.getStreetColour('service') + '"></i> Service <br>';
        legend.innerHTML += '<i style="background:' + streetsHelper.getStreetColour('steps') + '"></i> Steps <br>';
        legend.innerHTML += '<i style="background:' + streetsHelper.getStreetColour('tertiary') + '"></i> Tertiary <br>';
        legend.innerHTML += '<i style="background:' + streetsHelper.getStreetColour('unclassified') + '"></i> Unclassified <br>';

        return legend;
    };
    streetLegend.addTo(map);
}
</script>

<style>

    #buildings-map {
        height: 100%;
        margin: 0;
        position: relative;
        width: 70%;
    }

    .building-img {
        position: absolute;
        top: 1em;
        right: 1em;
    }

    .leaflet-bottom {
        z-index: 1;
    }

    .leaflet-marker-icon {
        border-radius: 20px;
    }

    .mi {
        height: 100%;
        margin: auto;
        display: flex;
        justify-content: center;
        border-radius: 20px;
    }

    .leaflet-control {
        pointer-events: visiblePainted !important;
    }

    .leaflet-popup-content {
        padding: 20px !important;
    }


    .leaflet-control-layers-toggle.leaflet-layerstree-named-toggle {
        margin: 2px 5px;
        width: auto;
        height: auto;
        background-image: none;
    }

    .leaflet-layerstree-node {
    }

    .leaflet-layerstree-header input{
        margin-left: 0px;
    }


    .leaflet-layerstree-header {
    }

    .leaflet-layerstree-header-pointer {
        cursor: pointer;
    }

    .leaflet-layerstree-header label {
        display: inline-block;
        cursor: pointer;
    }

    .leaflet-layerstree-header-label {
    }

    .leaflet-layerstree-header-name {
    }

    .leaflet-layerstree-header-space {
    }

    .leaflet-layerstree-children {
        padding-left: 10px;
    }

    .leaflet-layerstree-children-nopad {
        padding-left: 0px;
    }

    .leaflet-layerstree-closed {
    }

    .leaflet-layerstree-opened {
    }

    .leaflet-layerstree-hide {
        display: none;
    }

    .leaflet-layerstree-nevershow {
        display: none;
    }

    .leaflet-layerstree-expand-collapse {
        cursor: pointer;
    }

</style>
