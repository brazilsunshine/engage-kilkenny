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

    </div>
</template>

<script>
import {
    MAX_ZOOM,
    MIN_ZOOM,
} from '../../constants';

import L from 'leaflet';
import './SmoothWheelZoom.js';

import { mapHelper } from '../../maps/mapHelpers';
import { buildingsHelper } from "../../maps/buildingsHelper";
import { streetsHelper } from "../../maps/streetsHelper";
import { wallsHelper } from "../../maps/wallsHelper";

import SideMapContainer from "../../components/global/SideMapContainer";

var map;

var buildings, buildingsWithoutYear;

var streets, streetTypes;
var corridor, footway, path, pedestrian, residential, secondary, service, steps, tertiary, unclassified;
var streetsMaterial;

var walls, historic, tomb, streetLamp, oghamStone, memorial, cityGate, archaeological, uncategorised;

var art, statue, stainedGlassWindow, sculpture, relief, mural, graffiti;

var points, wateringPlace, wasteBasket, vendingMachines, training, toilets, theatre, telephone, studio,
    socialCentre, restaurant, recycling, publicOffice, pub, postOffice, postBox, placeOfWorship,
    pharmacy, parkingEntrance, parking, musicSchool, motorcycleParking, marketplace, library,
    kitchen, gritBin, fountain, fastFood, doctors, dentist, communityCentre, college, clock,
    chargingStation, carWash, cafe, bicyclingParking, bench, bar, bank, atm, artsCentre, courtsCentre;

// todo - var parks, carParks, publicBuildings, stories;

var layerController;

const green_dot = L.icon({
    iconUrl: './images/vendor/leaflet/dist/dot.png',
    iconSize: [10, 10]
});

const grey_dot = L.icon({
    iconUrl: './images/vendor/leaflet/dist/grey-dot.jpg',
    iconSize: [13, 10]
});

/**
 * Create the point to display for each piece of non-art data
 */
function createGreenDotIcon (feature, latlng)
{
    const x = [latlng.lat, latlng.lng];

    return L.marker(x, { icon: green_dot });
}

/**
 * Create the point to display for each piece of Art data
 */
function createGreyDotIcon (feature, latlng)
{
    const x = [latlng.lat, latlng.lng];

    return L.marker(x, { icon: grey_dot });
}


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
                        label: ' Street Material',
                        layer: streetsMaterial
                    }
                ]
            },
            {
                label: 'Historic',
                selectAllCheckbox: false,
                children: [
                    { label: ' Walls', layer: walls },
                    {
                        label: ' Other',
                        layer: historic,
                        children: [
                            { label: ' Archealogical', layer: archaeological },
                            { label: ' City Gate', layer: cityGate },
                            { label: ' Memorial', layer: memorial },
                            { label: ' Ogham Stone', layer: oghamStone },
                            { label: ' Street Lamp', layer: streetLamp },
                            { label: ' Tomb', layer: tomb },
                            { label: ' Uncategorised', layer: uncategorised },
                        ]
                    }
                ]
            },
            {
                label: ' Art',
                selectAllCheckbox: true,
                children: [
                    { label: ' Graffiti', layer: graffiti },
                    { label: ' Mural', layer: mural },
                    { label: ' Relief', layer: relief },
                    { label: ' Sculpture', layer: sculpture },
                    { label: ' Stained Glass', layer: stainedGlassWindow },
                    { label: ' Statue', layer: statue }
                ]
            },
            {
                label: ' Amenity',
                selectAllCheckbox: true,
                children: [
                    { label: ' Arts Centre', layer: artsCentre },
                    { label: ' ATM', layer: atm },
                    { label: ' Bank', layer: bank },
                    { label: ' Bench', layer: bench },
                    { label: ' Bicycle parking', layer: bicyclingParking },
                    { label: ' Cafe', layer: cafe },
                    { label: ' Car Wash', layer: carWash },
                    { label: ' Charging Station', layer: chargingStation },
                    { label: ' Clock', layer: clock },
                    { label: ' College', layer: college },
                    { label: ' Community Centre', layer: communityCentre },
                    { label: ' Dentist', layer: dentist },
                    { label: ' Doctors', layer: doctors },
                    { label: ' Fast Food', layer: fastFood },
                    { label: ' Fountain', layer: fountain },
                    { label: ' Grit Bin', layer: gritBin },
                    { label: ' Kitchen', layer: kitchen },
                    { label: ' Library', layer: library },
                    { label: ' Marketplace', layer: marketplace },
                    { label: ' Motorcycle Parking', layer: motorcycleParking },
                    { label: ' Music School', layer: musicSchool },
                    { label: ' Parking', layer: parking },
                    { label: ' Parking Entrance', layer: parkingEntrance },
                    { label: ' Pharmacy', layer: pharmacy },
                    { label: ' Place of Worship', layer: placeOfWorship },
                    { label: ' Post Box', layer: postBox },
                    { label: ' Post Office', layer: postOffice },
                    { label: ' Pub', layer: pub },
                    { label: ' Public Office', layer: publicOffice },
                    { label: ' Restaurant', layer: restaurant },
                    { label: ' Social Centre', layer: socialCentre },
                    { label: ' Studio', layer: studio },
                    { label: ' Telephone', layer: telephone },
                    { label: ' Toilets', layer: toilets },
                    { label: ' Training', layer: training },
                    { label: ' Vending Machine', layer: vendingMachines },
                    { label: ' Waste Basket', layer: wasteBasket },
                    { label: ' Watering Place', layer: wateringPlace },
                ]
            }
        ]
    }

    layerController = L.control.layers.tree(null, overlayLayersTree).addTo(map);
}

export default {
    name: 'BuildingsMap',
    components: {
        SideMapContainer,
    },
    data () {
        return {
            building: null,
            street: null,
            stories: [],
            buildingsKey: 0
        };
    },
    computed: {
        /**
         * Return True if a building is selected
         */
        buildingIsSelected ()
        {
            console.log('hasBuilding', window.buildingsMap?.hasOwnProperty('building'));
            return window.buildingsMap?.hasOwnProperty('building');
        }
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

        window.buildingsMap.map = map;

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
        addWallsLayer(this.$store.state.globalmap.walls.features, this.$store.state.globalmap.points.features);
        addPointsLayers(this.$store.state.globalmap.points.features);
        addArtLayer(this.$store.state.globalmap.points.features);

        window.buildingsMap.buildings = buildings;

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
        // await axios.get('/check-building', {
        //     params: {
        //         osm_id: building.osm_id
        //     }
        // })
        // .then(response => {
        //     console.log('check_building', response);
        //
        //     if (response.data.success) {
        //         window.buildingsMap.stories = response.data.stories;
        //     }
        // })
        // .catch(error => {
        //     console.error('check_building', error);
        // });

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
    streetTypes = L.geoJSON(null, { onEachFeature: onEachStreetType });
    // Each Filtered Type (10)
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
        onEachFeature: onEachStreetMaterial
    });
    const filteredStreetsMaterial = streetsHelper.getFilteredArray(streetsArray, 'material');
    streetsMaterial.addData({ features: filteredStreetsMaterial, type: "FeatureCollection" });
}

/**
 *
 */
function onEachStreetType (feature, layer)
{
    const colour = streetsHelper.getStreetColour(feature.properties.highway);

    if (colour)
    {
        layer.setStyle({
            fillOpacity: 0.5,
            color: colour
        });
    }

    layer.on('click', function (e)
    {
        const { str, street } = streetsHelper.getStringObject(feature.properties);

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

        const colour = streetsHelper.getStreetColour(feature.properties['highway']);

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

function addPointsLayers (pointsArray)
{
    // 1. Create empty L.geoJSON layers
    // All Types
    points = L.geoJson(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    // Each Filtered Type (41)
    artsCentre = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    atm = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    bank = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    bar = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    bench = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    bicyclingParking = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    cafe = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    carWash = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    chargingStation = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    clock = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    college = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    communityCentre = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    dentist = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    doctors = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    fastFood = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    fountain = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    gritBin = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    kitchen = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    library = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    marketplace = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    motorcycleParking = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    musicSchool = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    parking = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    parkingEntrance = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    pharmacy = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    placeOfWorship = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    postBox = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    postOffice = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    pub = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    publicOffice = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    recycling = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    restaurant = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    socialCentre = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    studio = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    telephone = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    theatre = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    toilets = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    training = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    vendingMachines = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    wasteBasket = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });
    wateringPlace = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreenDotIcon });

    // 2. Create a filtered geojson array of each layer we want to use
    // Get array of streets of column name "highway"
    const filteredPointsArray = streetsHelper.getFilteredArray(pointsArray, 'amenity');
    // Use filtered array to find values within the column
    const artsCentreArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'arts_centre');
    const atmArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'atm');
    const bankArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'bank');
    const barArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'bar');
    const benchArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'bench');
    const bicycleParkingArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'bicycle_parking');
    const cafeArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'cafe');
    const carWashArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'car_wash');
    const chargingStationArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'charging_station');
    const clockArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'clock');
    const collegeArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'college');
    const communityCentreArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'community_centre');
    const dentistArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'dentist');
    const doctorArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'doctor');
    const fastFoodArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'fast_food');
    const fountainArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'fountain');
    const gritBinArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'grit_bin');
    const kitchenArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'kitchen');
    const libraryArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'library');
    const marketPlaceArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'marketplace');
    const motorcycleParkingArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'motorcycle_parking');
    const musicSchoolArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'music_school'); // maybe remove _
    const parkingArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'parking');
    const parkingEntranceArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'parking_entrance');
    const pharmacyArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'pharmacy');
    const placeOfWorshipArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'place_of_worship');
    const postBoxArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'post_box');
    const postOfficeArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'post_office');
    const pubArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'pub');
    const publicOfficeArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'public_office');
    const recyclingArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'recycling');
    const restaurantArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'restaurant');
    const socialCentreArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'social_centre');
    const studioArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'studio');
    const telephoneArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'telephone');
    const theatreArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'theatre');
    const toiletsArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'toilets');
    const trainingArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'training');
    const vendingMachineArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'vending_machine');
    const wasteBasketArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'waste_basket');
    const wateringPlaceArray = streetsHelper.getStreetByType(filteredPointsArray, 'amenity', 'watering_place');

    points.addData({ features: filteredPointsArray, type: "FeatureCollection" });
    atm.addData({ features: atmArray, type: "FeatureCollection" });
    artsCentre.addData({ features: artsCentreArray, type: "FeatureCollection" });
    bank.addData({ features: bankArray, type: "FeatureCollection" });
    bar.addData({ features: barArray, type: "FeatureCollection" });
    bench.addData({ features: benchArray, type: "FeatureCollection" });
    bicyclingParking.addData({ features: bicycleParkingArray, type: "FeatureCollection" });
    cafe.addData({ features: cafeArray, type: "FeatureCollection" });
    carWash.addData({ features: carWashArray, type: "FeatureCollection" });
    chargingStation.addData({ features: chargingStationArray, type: "FeatureCollection" });
    clock.addData({ features: clockArray, type: "FeatureCollection" });
    college.addData({ features: collegeArray, type: "FeatureCollection" });
    communityCentre.addData({ features: communityCentreArray, type: "FeatureCollection" });
    dentist.addData({ features: dentistArray, type: "FeatureCollection" });
    doctors.addData({ features: doctorArray, type: "FeatureCollection" });
    fastFood.addData({ features: fastFoodArray, type: "FeatureCollection" });
    fountain.addData({ features: fountainArray, type: "FeatureCollection" });
    gritBin.addData({ features: gritBinArray, type: "FeatureCollection" });
    kitchen.addData({ features: kitchenArray, type: "FeatureCollection" });
    library.addData({ features: libraryArray, type: "FeatureCollection" });
    marketplace.addData({ features: marketPlaceArray, type: "FeatureCollection" });
    motorcycleParking.addData({ features: motorcycleParkingArray, type: "FeatureCollection" });
    musicSchool.addData({ features: musicSchoolArray, type: "FeatureCollection" });
    parking.addData({ features: parkingArray, type: "FeatureCollection" });
    parkingEntrance.addData({ features: parkingEntranceArray, type: "FeatureCollection" });
    pharmacy.addData({ features: pharmacyArray, type: "FeatureCollection" });
    placeOfWorship.addData({ features: placeOfWorshipArray, type: "FeatureCollection" });
    postBox.addData({ features: postBoxArray, type: "FeatureCollection" });
    postOffice.addData({ features: postOfficeArray, type: "FeatureCollection" });
    pub.addData({ features: pubArray, type: "FeatureCollection" });
    publicOffice.addData({ features: publicOfficeArray, type: "FeatureCollection" });
    recycling.addData({ features: recyclingArray, type: "FeatureCollection" });
    restaurant.addData({ features: restaurantArray, type: "FeatureCollection" });
    socialCentre.addData({ features: socialCentreArray, type: "FeatureCollection" });
    studio.addData({ features: studioArray, type: "FeatureCollection" });
    telephone.addData({ features: telephoneArray, type: "FeatureCollection" });
    theatre.addData({ features: theatreArray, type: "FeatureCollection" });
    toilets.addData({ features: toiletsArray, type: "FeatureCollection" });
    training.addData({ features: trainingArray, type: "FeatureCollection" });
    wasteBasket.addData({ features: wasteBasketArray, type: "FeatureCollection" });
    vendingMachines.addData({ features: vendingMachineArray, type: "FeatureCollection" });
    wateringPlace.addData({ features: wateringPlaceArray, type: "FeatureCollection" });
}

function onEachPoint (feature, layer)
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

function addWallsLayer (wallsArray, pointsArray)
{
    walls = L.geoJSON(null, {
        onEachFeature: onEachWalls
    }).addTo(map);

    walls.addData({ features: wallsArray, type: "FeatureCollection" });

    // Parent array
    historic = L.geoJSON(null, { onEachFeature: onEachWalls, pointToLayer: createGreenDotIcon });
    // Child arrays from historic
    archaeological = L.geoJSON(null, { onEachFeature: onEachWalls, pointToLayer: createGreenDotIcon });
    cityGate = L.geoJSON(null, { onEachFeature: onEachWalls, pointToLayer: createGreenDotIcon });
    memorial = L.geoJSON(null, { onEachFeature: onEachWalls, pointToLayer: createGreenDotIcon });
    oghamStone = L.geoJSON(null, { onEachFeature: onEachWalls, pointToLayer: createGreenDotIcon });
    streetLamp = L.geoJSON(null, { onEachFeature: onEachWalls, pointToLayer: createGreenDotIcon });
    tomb = L.geoJSON(null, { onEachFeature: onEachWalls, pointToLayer: createGreenDotIcon });
    uncategorised = L.geoJSON(null, { onEachFeature: onEachWalls, pointToLayer: createGreenDotIcon });

    // All historic points
    const filteredHistoricArray = streetsHelper.getFilteredArray(pointsArray, 'historic');
    historic.addData({ features: filteredHistoricArray, type: "FeatureCollection" });

    // Filter all historic points by values
    const archeologicalArray = streetsHelper.getStreetByType(pointsArray, 'historic', 'archaeological_site');
    const cityGateArray = streetsHelper.getStreetByType(pointsArray, 'historic', 'city_gate');
    const memorialArray = streetsHelper.getStreetByType(pointsArray, 'historic', 'memorial');
    const oghamStoneArray = streetsHelper.getStreetByType(pointsArray, 'historic', 'ogham_stone');
    const streetLampArray = streetsHelper.getStreetByType(pointsArray, 'historic', 'street_lamp');
    const tombArray = streetsHelper.getStreetByType(pointsArray, 'historic', 'tomb');
    const uncategorisedArray = streetsHelper.getStreetByType(pointsArray, 'historic', 'yes');

    archaeological.addData({ features: archeologicalArray, type: "FeatureCollection" });
    cityGate.addData({ features: cityGateArray, type: "FeatureCollection" });
    memorial.addData({ features: memorialArray, type: "FeatureCollection "});
    oghamStone.addData({ features: oghamStoneArray, type: "FeatureCollection" });
    streetLamp.addData({ features: streetLampArray, type: "FeatureCollection" });
    tomb.addData({ features: tombArray, type: "FeatureCollection" });
    uncategorised.addData({ features: uncategorisedArray, type: "FeatureCollection" });
}

function addArtLayer (pointsArray)
{
    art = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreyDotIcon });

    // All art points
    const filteredArtArray = streetsHelper.getFilteredArray(pointsArray, 'artwork_type');
    historic.addData({ features: filteredArtArray, type: "FeatureCollection" });

    graffiti = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreyDotIcon });
    mural = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreyDotIcon });
    relief = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreyDotIcon });
    sculpture = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreyDotIcon });
    stainedGlassWindow = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreyDotIcon });
    statue = L.geoJSON(null, { onEachFeature: onEachPoint, pointToLayer: createGreyDotIcon });

    // Filtered art points
    const graffitiArray = streetsHelper.getStreetByType(filteredArtArray, 'artwork_type', 'graffiti');
    const muralArray = streetsHelper.getStreetByType(filteredArtArray, 'artwork_type', 'mural');
    const reliefArray = streetsHelper.getStreetByType(filteredArtArray, 'artwork_type', 'relief');
    const scultpureArray = streetsHelper.getStreetByType(filteredArtArray, 'artwork_type', 'sculpture');
    const stainedGlassArray = streetsHelper.getStreetByType(filteredArtArray, 'artwork_type', 'stained-glass_window');
    const statueArray = streetsHelper.getStreetByType(filteredArtArray, 'artwork_type', 'mural');

    graffiti.addData({ features: graffitiArray, type: "FeatureCollection" });
    mural.addData({ features: muralArray, type: "FeatureCollection" });
    relief.addData({ features: reliefArray, type: "FeatureCollection" });
    sculpture.addData({ features: scultpureArray, type: "FeatureCollection" });
    stainedGlassWindow.addData({ features: stainedGlassArray, type: "FeatureCollection" });
    statue.addData({ features: statueArray, type: "FeatureCollection" });
}

/**
 * On each walls feature
 */
function onEachWalls (feature, layer)
{
    layer.on("mouseover", function(e) {
        layer.setStyle({
            fillOpacity: 0.4,
            color: 'yellow'
        });
    });

    layer.on("mouseout",function(e) {
        layer.setStyle({
            fillOpacity: 0.5,
            color: '#3388ff'
        });
    });

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
        /*width: 550px !important;*/
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
