<template>
    <div class="h100 flex">
        <div id="review-map" />
    </div>
</template>

<script>
import L from 'leaflet';
import '../../../views/global/SmoothWheelZoom.js';
import { MAX_ZOOM, MIN_ZOOM } from "../../../constants";

var map, building, buildings;

export default {
    name: "ReviewMap",
    mounted () {
        /** 0: Bind variable outside of vue scope */
        window.buildingsMap = this;

        /** 1. Create map object */
        map = L.map('review-map', {
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
        // addBuildingLayers(this.$store.state.globalmap.buildings.features);
    }
}
</script>

<style scoped>

    #review-map {
        height: 100%;
        margin: 0;
        position: relative;
        width: 100%;
    }

</style>
