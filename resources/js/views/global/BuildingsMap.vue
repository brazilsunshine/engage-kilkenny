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
import SideMapContainer from "../../components/global/SideMapContainer";

var map;

var buildings;
var buildingsWithoutYear;

var streets;
var streetTypes;

var corridor;
var footway;
var path;
var pedestrian;
var residential;
var secondary;
var service;
var steps;
var tertiary;
var unclassified;

var streetsMaterial;

// todo
var parks;
var carParks;
var publicBuildings;
var stories;

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
                            { label: ' Residentail', layer: residential },
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
            }
        ]
    }

    layerController = L.control.layers.tree(null, overlayLayersTree).addTo(map);
}

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
        if (buildingsDate >= 1200 && buildingsDate <= 1300) return '#fa6e6e';
        else if (buildingsDate >= 1301 && buildingsDate <= 1650) return '#e36386';
        else if (buildingsDate >= 1651 && buildingsDate <= 1765) return '#c16095';
        else if (buildingsDate >= 1766 && buildingsDate <= 1815) return '#9a609b';
        else if (buildingsDate >= 1816 && buildingsDate <= 1916) return '#715e96';
        else if (buildingsDate >= 1917 && buildingsDate <= 1950) return '#4d5a86';
        else if (buildingsDate >= 1951 && buildingsDate <= 2000) return '#345271';
        else if (buildingsDate >= 2001 && buildingsDate <= 2022) return '#2a4858';
    }

    return null;
}

/**
 * Helper function
 *
 * Get the colour for a street based on its highway/type
 *
 * @param streetType
 * @returns {string|null}
 */
function getStreetColour (streetType)
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
}

/**
 * On each street feature
 */
function onEachStreet (feature, layer)
{
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
        layer.setStyle({
            fillOpacity: 0,
            color: '#3388ff'
        });
    });
}

/**
 *
 */
function onEachStreetType (feature, layer)
{
    const colour = getStreetColour(feature.properties.highway);

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

        const colour = getStreetColour(feature.properties['highway']);

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
}

/**
 * On Each Street Material Layer
 */
function onEachStreetMaterial (feature, layer)
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
}

/**
 * On each building feature
 */
function onEachBuilding (feature, layer)
{
    const colour = getBuildingsColour(feature.properties.NEWDATE);

    if (colour)
    {
        layer.setStyle({
            fillOpacity: 0.5,
            color: colour
        });
    }

    layer.on('click', async function (e)
    {
        const keys = Object.keys(feature.properties);

        let building = {};
        let str = "";

        keys.forEach(key => {
            if (feature.properties[key])
            {
                building[key] = feature.properties[key];

                str += key + ": " + feature.properties[key] + " <br> ";
            }
        })

        console.log(building);

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

        const colour = getBuildingsColour(feature.properties.NEWDATE);

        if (colour)
        {
            layer.setStyle({
                fillOpacity: 0.5,
                color: colour
            });
        }
        else if (feature.properties.hasStory)
        {
            layer.setStyle({
                fillOpacity: 0.5,
                color: '#2ecc71'
            });
        }
        else
        {
            layer.setStyle({
                fillOpacity: 0,
                color: '#3388ff'
            });
        }
    });
}

function onEachBuildingWithoutYear (feature, layer)
{

}

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

        map.attributionControl.addAttribution(
            'Litter data &copy OpenLitterMap & Contributors '
            + new Date().getFullYear()
            + ' Architecture Data &copy NIAH'
        );

        // Add the layers
        addBuildingLayers(this.$store.state.globalmap.buildings.features);
        addStreetLayers(this.$store.state.globalmap.streets.features);

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
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: filteredBuildingsWithYear,
        type: "FeatureCollection"
    });

    buildingsWithoutYear = L.geoJSON(null, {
        onEachFeature: onEachBuilding
    });

    const filteredBuildingsWithoutYear = buildingsArray.filter(feature => {
        if (!feature.properties.hasOwnProperty('NEWDATE'))
        {
            return feature;
        }
    });

    buildingsWithoutYear.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: filteredBuildingsWithoutYear,
        type: "FeatureCollection"
    });
}

/**
 * Use the array of streets from the backend
 *
 * Create many layers that can be turned on and off
 */
function addStreetLayers (streetsArray)
{
    // 1. Create empty L.geoJSON layers for every layer we want to use

    // All Types
    streetTypes = L.geoJSON(null, { onEachFeature: onEachStreetType });
    // Each Type (10)
    corridor = L.geoJSON(null, { onEachFeature: onEachStreetType });
    footway = L.geoJSON(null, { onEachFeature: onEachStreetType });
    path = L.geoJSON(null, { onEachFeature: onEachStreetType });
    pedestrian = L.geoJSON(null, { onEachFeature: onEachStreetType });
    residential = L.geoJSON(null, { onEachFeature: onEachStreetType });
    secondary = L.geoJSON(null, { onEachFeature: onEachStreetType });
    service = L.geoJSON(null, { onEachFeature: onEachStreetType });
    steps = L.geoJSON(null, { onEachFeature: onEachStreetType });
    tertiary = L.geoJSON(null, { onEachFeature: onEachStreetType });
    unclassified = L.geoJSON(null, { onEachFeature: onEachStreetType });

    // 2. Create a filtered geojson array of each layer we want to use
    // All Types
    const filteredStreetTypes = streetsArray.filter(streetFeature => {
        if (streetFeature.properties.hasOwnProperty('highway')) {
            return streetFeature;
        }
    });
    const corridorArray = filteredStreetTypes.filter(feature => {
        if (feature.properties['highway'] === 'corridor') {
            return feature;
        }
    });
    const footwayArray = filteredStreetTypes.filter(feature => {
        if (feature.properties['highway'] === 'footway') {
            return feature;
        }
    });
    const pathArray = filteredStreetTypes.filter(feature => {
        if (feature.properties['highway'] === 'path') {
            return feature;
        }
    });
    const pedestrianArray = filteredStreetTypes.filter(feature => {
        if (feature.properties['highway'] === 'pedestrian') {
            return feature;
        }
    });
    const residentialArray = filteredStreetTypes.filter(feature => {
        if (feature.properties['highway'] === 'residential') {
            return feature;
        }
    });
    const secondaryArray = filteredStreetTypes.filter(feature => {
        if (feature.properties['highway'] === 'secondary') {
            return feature;
        }
    });
    const serviceArray = filteredStreetTypes.filter(feature => {
        if (feature.properties['highway'] === 'service') {
            return feature;
        }
    });
    const stepsArray = filteredStreetTypes.filter(feature => {
        if (feature.properties['highway'] === 'steps') {
            return feature;
        }
    });
    const tertiaryArray = filteredStreetTypes.filter(feature => {
        if (feature.properties['highway'] === 'tertiary') {
            return feature;
        }
    });
    const unclassifiedArray = filteredStreetTypes.filter(feature => {
        if (feature.properties['highway'] === 'unclassified') {
            return feature;
        }
    });

    // 3. Add the filtered array to the empty geojson type
    streetTypes.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: filteredStreetTypes,
        type: "FeatureCollection"
    });
    corridor.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: corridorArray,
        type: "FeatureCollection"
    });
    footway.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: footwayArray,
        type: "FeatureCollection"
    });
    path.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: pathArray,
        type: "FeatureCollection"
    });
    pedestrian.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: pedestrianArray,
        type: "FeatureCollection"
    });
    residential.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: residentialArray,
        type: "FeatureCollection"
    });
    secondary.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: secondaryArray,
        type: "FeatureCollection"
    });
    service.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: serviceArray,
        type: "FeatureCollection"
    });
    steps.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: stepsArray,
        type: "FeatureCollection"
    });
    tertiary.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: tertiaryArray,
        type: "FeatureCollection"
    });
    unclassified.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: unclassifiedArray,
        type: "FeatureCollection"
    });

    streetsMaterial = L.geoJSON(null, {
        onEachFeature: onEachStreetMaterial
    });
    const filteredStreetsMaterial = streetsArray.filter(streetFeature => {
        if (streetFeature.properties.hasOwnProperty('material'))
        {
            return streetFeature;
        }
    });
    streetsMaterial.addData({
        crs: {
            properties: {
                name: "urn:ogc:def:crs:OGC:1.3:CRS84"
            },
            type: "name"
        },
        features: filteredStreetsMaterial,
        type: "FeatureCollection"
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
        legend.innerHTML += '<i style="background:' + getBuildingsColour(1200) + '"></i> 1200 - 1300' + '<br>';
        legend.innerHTML += '<i style="background:' + getBuildingsColour(1301) + '"></i> 1301 - 1650' + '<br>';
        legend.innerHTML += '<i style="background:' + getBuildingsColour(1651) + '"></i> 1651 - 1765' + '<br>';
        legend.innerHTML += '<i style="background:' + getBuildingsColour(1790) + '"></i> 1766 - 1815' + '<br>';
        legend.innerHTML += '<i style="background:' + getBuildingsColour(1816) + '"></i> 1816 - 1916' + '<br>';
        legend.innerHTML += '<i style="background:' + getBuildingsColour(1816) + '"></i> 1917 - 1950' + '<br>';
        legend.innerHTML += '<i style="background:' + getBuildingsColour(1816) + '"></i> 1951 - 2000' + '<br>';
        legend.innerHTML += '<i style="background:' + getBuildingsColour(1816) + '"></i> 2001 - 2022' + '<br>';

        return legend;
    };
    legend.addTo(map);

    // Streets - Type of street (highway)
    let streetLegend = L.control({
        position: 'bottomleft'
    });
    streetLegend.onAdd = function (map) {
        let legend = L.DomUtil.create('div', 'info legend street-legend');

        legend.innerHTML += '<i style="background:' + getStreetColour('corridor') + '"></i> Corridor <br>';
        legend.innerHTML += '<i style="background:' + getStreetColour('footway') + '"></i> Footway <br>';
        legend.innerHTML += '<i style="background:' + getStreetColour('path') + '"></i> Path <br>';
        legend.innerHTML += '<i style="background:' + getStreetColour('pedestrian') + '"></i> Pedestrial <br>';
        legend.innerHTML += '<i style="background:' + getStreetColour('residential') + '"></i> Residential <br>';
        legend.innerHTML += '<i style="background:' + getStreetColour('secondary') + '"></i> Secondary <br>';
        legend.innerHTML += '<i style="background:' + getStreetColour('service') + '"></i> Service <br>';
        legend.innerHTML += '<i style="background:' + getStreetColour('steps') + '"></i> Steps <br>';
        legend.innerHTML += '<i style="background:' + getStreetColour('tertiary') + '"></i> Tertiary <br>';
        legend.innerHTML += '<i style="background:' + getStreetColour('unclassified') + '"></i> Unclassified <br>';

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
