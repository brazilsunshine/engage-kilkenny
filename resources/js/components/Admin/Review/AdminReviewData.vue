<template>
    <div class="mb1">
        <strong>
            Admin: There are {{ stories.length }} stories and {{ reviewData.length }} data items to review
        </strong>

        <select v-model="toReview" class="input mt1 mb1">
            <option value="stories">Review Stories</option>
            <option value="data">Review Data</option>
        </select>

        <ReviewStories
            v-if="toReview === 'stories'"
            :stories="stories"
        />

        <ReviewData
            v-if="toReview === 'data'"
            :review-data="reviewData"
        />

    </div>
</template>

<script>
import ReviewStories from "./ReviewStories";
import ReviewData from "./ReviewData";

export default {
    name: "AdminReviewData",
    components: {
        ReviewStories,
        ReviewData
    },
    data () {
        return {
            toReview: "stories"
        };
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
}
</script>

<style scoped>

</style>
