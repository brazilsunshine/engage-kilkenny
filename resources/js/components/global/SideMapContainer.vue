<template>
    <div class="map-side-container">
        <h1 class="title is-3 pt1">Your Historic Town Center</h1>

        <p class="mb1">Do you have any nice stories about this building?</p>

        <textarea
            v-model="story"
            rows="4"
            class="textarea mb1"
            placeholder="Once upon a time..."
        />

        <button
            class="button is-medium is-primary"
            :class="loading ? 'is-loading' : ''"
            @click="addStory"
            :disabled="loading"
        >
            Add Story
        </button>
    </div>
</template>

<script>
export default {
    name: "SideMapContainer",
    data () {
        return {
            story: "",
            loading: false
        };
    },
    methods: {
        async addStory () {

            this.loading = true;

            await axios.post('/add-story', {
                story: this.story
            })
            .then(response => {
                console.log(response);

                if (response.data.success) {
                    console.log('The request was successful!');
                } else {
                    console.log('Did not work');
                }
            })
            .catch(error => {
                console.log(error);
            });

            this.loading = false;
        }
    }
}
</script>

<style scoped>

    .map-side-container {
        background-color: white;
        height: 100%;
        width: 30%;
        text-align: center;
        padding-left: 1em;
        padding-right: 1em;
    }

</style>
