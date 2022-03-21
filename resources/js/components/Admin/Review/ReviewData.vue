<template>
    <div v-if="reviewData.length > 0">
        <p class="mb1">There are <strong>{{ reviewData.length }}</strong> data items to review</p>

        <p>Key: {{ reviewData[0].key }}</p>
        <p class="mb1">Value: {{ reviewData[0].value }}</p>

        <button class="button is-small is-primary" @click="loadBuilding">
            Load Building
        </button>

    </div>
</template>

<script>
export default {
    name: "ReviewData",
    props: [
        'reviewData'
    ],
    methods: {
        /**
         * Todo:
         * - Highlight this building
         * - Zoom to this building
         */
        loadBuilding ()
        {
            const osmId = this.reviewData[0].osm_id;

            window.buildingsMap.buildings.eachLayer(function (layer) {

                if (parseInt(layer.feature.properties.osm_id) === osmId) {

                    console.log(layer);

                    // const coords = layer._renderer._center;
                    // console.log({ coords });
                    // const latLng = [coords.lat, coords.lng];
                    // console.log({ latLng });

                    layer.setStyle({
                        fillColor: 'yellow'
                    });

                    // window.buildingsMap.map.flyTo(latLng, 18, {
                    //     animate: true,
                    //     duration: 5
                    // });
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
