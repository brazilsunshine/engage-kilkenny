<template>
    <div class="columns global-map-container">

        <div class="column is-half is-offset-1 h100">
            <ReviewMap />
        </div>

        <div class="column is-two-thirds is-offset-2 pt3">

            <div v-if="stories.length === 0 && reviewData.leading === 0">
                <p class="title is-4 mb1">There are no Stories or Data to verify</p>
            </div>

            <div v-else>
                <ReviewData
                    :reviewData="reviewData"
                />
                <ReviewStories
                    :stories="stories"
                />
            </div>
        </div>
    </div>
</template>

<script>
import ReviewMap from "../../components/Admin/Review/ReviewMap";
import ReviewData from "../../components/Admin/Review/ReviewData";
import ReviewStories from "../../components/Admin/Review/ReviewStories";

export default {
    name: "AdminReviewPage",
    async created () {
        await this.$store.dispatch('ADMIN_GET_STORIES');

        await this.$store.dispatch('ADMIN_GET_DATA');
    },
    components: {
        ReviewMap,
        ReviewData,
        ReviewStories
    },
    computed: {
        /**
         * Array of buildings key: value and images that need to be reviewed
         */
        reviewData ()
        {
            return this.$store.state.admin.data;
        },

        /**
         * Array of stories attached to buildings that need to be reviewed
         */
        stories ()
        {
            return this.$store.state.admin.stories;
        }
    },
    methods: {

    }
}
</script>

<style scoped>

    .global-map-container {
        height: calc(100% - 72px);
        margin: 0;
        position: relative;
        z-index: 1;
    }

</style>
