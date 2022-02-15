<template>
    <div class="h100 flex">
        <!-- The map & data -->
        <div id="buildings-map" ref="buildings-map" />

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

// Todo - fix this export bug (The request of a dependency is an expression...)
import glify from 'leaflet.glify';
import { mapHelper } from '../../maps/mapHelpers';
import SideMapContainer from "../../components/global/SideMapContainer";

var clickedBuilding;
var buildings;

var map;
var clusters;
var litterArtPoints;
var points;
var prevZoom = MIN_ZOOM;

var pointsLayerController;
var globalLayerController;
var pointsControllerShowing = false;
var globalControllerShowing = false;

const green_dot = L.icon({
    iconUrl: './images/vendor/leaflet/dist/dot.png',
    iconSize: [10, 10]
});

const grey_dot = L.icon({
    iconUrl: './images/vendor/leaflet/dist/grey-dot.jpg',
    iconSize: [13, 10]
});

/**
 * Create the point to display for each piece of Litter Art
 */
function createArtIcon (feature, latlng)
{
    const x = [latlng.lng, latlng.lat];

    return (feature.properties.verified === 2)
        ? L.marker(x, { icon: green_dot })
        : L.marker(x, { icon: grey_dot });
}

/**
 * Create the cluster or point icon to display for each feature
 */
function createClusterIcon (feature, latlng)
{
    if (!feature.properties.cluster)
    {
        return (feature.properties.verified === 2)
            ? L.marker(latlng, { icon: green_dot })
            : L.marker(latlng, { icon: grey_dot });
    }

    const count = feature.properties.point_count;
    const size = (count < MEDIUM_CLUSTER_SIZE)
        ? 'small'
            : count < LARGE_CLUSTER_SIZE
            ? 'medium'
            : 'large';

    const icon = L.divIcon({
        html: '<div class="mi"><span class="ma">' + feature.properties.point_count_abbreviated + '</span></div>',
        className: 'marker-cluster-' + size,
        iconSize: L.point(40, 40)
    });

    return L.marker(latlng, { icon });
}

/**
 * Layer controller when below ZOOM_CLUSTER_THRESHOLD
 */
function createGlobalGroups ()
{
    if (pointsControllerShowing)
    {
        map.removeControl(pointsLayerController);

        pointsControllerShowing = false;
    }

    if (!globalControllerShowing)
    {
        globalLayerController = L.control.layers(null, null).addTo(map);

        globalLayerController.addOverlay(clusters, 'Global');
        globalLayerController.addOverlay(litterArtPoints, 'Litter Art');

        globalControllerShowing = true;
    }
}

/**
 * Layer Controller when above ZOOM_CLUSTER_THRESHOLD
 */
function createPointGroups ()
{
    if (globalControllerShowing)
    {
        map.removeControl(globalLayerController)

        globalControllerShowing = false;
    }

    if (!pointsControllerShowing)
    {
        const overlays = {
            Alcohol: new L.LayerGroup(),
            Brands: new L.LayerGroup(),
            Coastal: new L.LayerGroup(),
            Coffee: new L.LayerGroup(),
            Dumping: new L.LayerGroup(),
            Food: new L.LayerGroup(),
            Industrial: new L.LayerGroup(),
            Other: new L.LayerGroup(),
            PetSurprise: new L.LayerGroup(),
            Sanitary: new L.LayerGroup(),
            Smoking: new L.LayerGroup(),
            SoftDrinks: new L.LayerGroup(),
        };

        pointsLayerController = L.control.layers(null, overlays).addTo(map);

        pointsControllerShowing = true;
    }
}

/**
 * Zoom to a cluster when it is clicked
 */
function onEachFeature (feature, layer)
{
    if (feature.properties.cluster)
    {
        layer.on('click', function (e)
        {
            const zoomTo = ((map.getZoom() + ZOOM_STEP) > MAX_ZOOM)
                ? MAX_ZOOM
                : (map.getZoom() + ZOOM_STEP);

            map.flyTo(e.latlng, zoomTo, {
                animate: true,
                duration: 2
            });
        });
    }
}

/**
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
        else if (buildingsDate >= 1500 && buildingsDate <= 1650)
        {
            return '#f00';
        }
        else if (buildingsDate >= 1651 && buildingsDate <= 1765)
        {
            return '#ff403f';
        }
        else if (buildingsDate === 1790)
        {
            return '#ff8080';
        }
        else if (buildingsDate >= 1815 && buildingsDate <= 1880)
        {
            return '#ff8080';
        }
    }

    return null;
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

export default {
    name: 'BuildingsMap',
    components: {
        SideMapContainer,
        LiveEvents
    },
    data () {
        return {
            building: null,
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

        const date = new Date();
        
        const year = date.getFullYear();

        /** 2. Add tiles, attribution, set limits */
        const mapLink = '<a href="https://openstreetmap.org">OpenStreetMap</a>';

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; ' + mapLink + ' & Contributors',
            maxZoom: MAX_ZOOM,
            minZoom: MIN_ZOOM
        }).addTo(map);

        map.attributionControl.addAttribution('Litter data &copy OpenLitterMap & Contributors ' + year + ' Clustering @ MapBox');

        buildings = L.geoJSON(null, {
            onEachFeature: onEachBuilding
        }).addTo(map);

        buildings.addData(this.$store.state.globalmap.buildingsGeojson);

        map.on('click', function (e) {
            window.buildingsMap.building = null;
        });

        // // Empty Layer Group that will receive the clusters data on the fly.
        // clusters = L.geoJSON(null, {
        //     pointToLayer: createClusterIcon,
        //     onEachFeature: onEachFeature,
        // }).addTo(map);
        //
        // clusters.addData(this.$store.state.globalmap.geojson.features);

        // litterArtPoints = L.geoJSON(null, {
        //     pointToLayer: createArtIcon,
        //     onEachFeature: onEachArtFeature
        // });
        //
        // litterArtPoints.addData(this.$store.state.globalmap.artData.features);
        //
        // map.on('moveend', this.update);

        // createGlobalGroups();

        map.on('overlayadd', this.update);
        map.on('overlayremove', this.update)

        let legend = L.control({
            position: 'bottomleft'
        });

        legend.onAdd = function (map) {

            let div = L.DomUtil.create('div', 'info legend');
            // grades = [1200, 1300, 1500, 1650, 1650, 1765, 1790, 1815, 1880];

            div.innerHTML += '<i style="background:' + getBuildingsColour(1200) + '"></i> 1200 - 1300' + '<br>';
            div.innerHTML += '<i style="background:' + getBuildingsColour(1500) + '"></i> 1350 - 1650' + '<br>';
            div.innerHTML += '<i style="background:' + getBuildingsColour(1651) + '"></i> 1651 - 1765' + '<br>';
            div.innerHTML += '<i style="background:' + getBuildingsColour(1790) + '"></i> 1790' + '<br>';
            div.innerHTML += '<i style="background:' + getBuildingsColour(1815) + '"></i> 1815 - 1880' + '<br>';

            return div;
        };

        legend.addTo(map);


    },
    methods: {
        /**
         * The user dragged or zoomed the map, or changed a category
         */
        async update ()
        {
            const bounds = map.getBounds();

            const bbox = {
                'left': bounds.getWest(),
                'bottom': bounds.getSouth(),
                'right': bounds.getEast(),
                'top': bounds.getNorth()
            };

            const zoom = Math.round(map.getZoom());

            // We don't want to make a request at zoom level 2-5 if the user is just panning the map.
            // At these levels, we just load all global data for now
            if (zoom === 2 && zoom === prevZoom) return;
            if (zoom === 3 && zoom === prevZoom) return;
            if (zoom === 4 && zoom === prevZoom) return;
            if (zoom === 5 && zoom === prevZoom) return;

            // Remove points when zooming out
            if (points)
            {
                clusters.clearLayers();
                points.remove();
            }

            // Get Clusters or Points
            if (zoom < CLUSTER_ZOOM_THRESHOLD)
            {
                createGlobalGroups();

                await axios.get('/global/clusters', {
                    params: {
                        zoom,
                        bbox
                    }
                })
                .then(response => {
                    console.log('get_clusters.update', response);

                    clusters.clearLayers();
                    clusters.addData(response.data);
                })
                .catch(error => {
                    console.error('get_clusters.update', error);
                });
            }
            else
            {
                createPointGroups()

                const layers = getActiveLayers();

                await axios.get('/global/points', {
                    params: {
                        zoom,
                        bbox,
                        layers
                    }
                })
                .then(response => {
                    console.log('get_global_points', response);

                    // Clear layer if prev layer is cluster.
                    if (prevZoom < CLUSTER_ZOOM_THRESHOLD)
                    {
                        clusters.clearLayers();
                    }

                    const data = response.data.features.map(feature => {
                        return [feature.geometry.coordinates[0], feature.geometry.coordinates[1]];
                    });

                    // New way using webGL
                    points = glify.points({
                        map,
                        data,
                        size: 10,
                        color: { r: 0.054, g: 0.819, b: 0.27, a: 1 }, // 14, 209, 69 / 255
                        click:  (e, point, xy) => {
                            const feature = response.data.features.find(f => {
                                return f.geometry.coordinates[0] === point[0]
                                    && f.geometry.coordinates[1] === point[1];
                            });

                            if (!feature) {
                                return;
                            }

                            return this.renderLeafletPopup(feature, e.latlng)
                        },
                    });
                })
                .catch(error => {
                    console.error('get_global_points', error);
                });
            }

            prevZoom = zoom;
        },

        /**
         * Helper method to create a Popup
         *
         * @param feature
         * @param latLng
         */
        renderLeafletPopup (feature, latLng)
        {
            const user = mapHelper.formatUserName(feature.properties.name, feature.properties.username);

            L.popup(mapHelper.popupOptions)
                .setLatLng(latLng)
                .setContent(
                    mapHelper.getMapImagePopupContent(
                        feature.properties.filename,
                        feature.properties.result_string,
                        feature.properties.datetime,
                        user,
                        feature.properties.team
                    )
                )
                .openOn(map);
        }
    }
};
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

</style>
