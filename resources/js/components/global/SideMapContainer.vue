<template>
    <div class="map-side-container">
        <h1 class="title is-3 pt1">Your Historic Town Center</h1>

        <div v-if="loggedIn">
            <p class="mb1">
                Do you have any nice stories about this building?
            </p>

            <input
                class="input mb1"
                v-model="title"
                placeholder="Give your story a nice title"
            />

            <textarea
                v-model="story"
                rows="4"
                class="textarea mb1"
                placeholder="Once upon a time..."
            />

            <input
                class="input mb1"
                v-model="date"
                placeholder="When did it happen"
            />

            <p v-if="showError" class="is-red mb1">
                {{ this.error }}
            </p>

            <button
                class="button is-medium is-primary"
                :class="loading ? 'is-loading' : ''"
                @click="addStory"
                :disabled="loading"
            >
                Add Story
            </button>
        </div>

        <div v-else>
            <p>Please log in to perform this action.</p>
        </div>

        <div
            v-if="hasStories"
        >
            <h1 class="title is-3 mt1 mb1">
                Here are some stories about this building!
            </h1>

            <div class="content">
                <ul>
                    <li v-for="story in stories" class="building-story">
                        <p>{{ story.story }}</p>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</template>

<script>
import Vue from "vue";

export default {
    name: "SideMapContainer",
    data () {
        return {
            title: "",
            story: "",
            date: "",
            loading: false,
            showError: false,
            error: ""
        };
    },
    computed: {
        /**
         * Return True if stories array is greater than 0
         */
        hasStories () {
            return (window.buildingsMap?.stories.length > 0);
        },

        /**
         * Return True or False to show if the user is logged in
         */
        loggedIn () {
            return this.$store.state.user.auth;
        },

        /**
         * Return array of any stories associated with the clicked building
         */
        stories () {
            return window.buildingsMap?.stories;
        }
    },
    methods: {
        /**
         * Send a post request to our database
         *
         * That adds a new story to the stories table
         */
        async addStory () {

            if (window.buildingsMap.building === null) {
                this.showError = true;

                this.error = "You must select a building to add a story to."

                return;
            }

            this.loading = true;

            await axios.post('/add-story', {
                story: this.story,
                title: this.title,
                date: this.date,
                osm_id: window.buildingsMap.building.osm_id
            })
            .then(response => {
                console.log(response);

                if (response.data.success) {
                    console.log('The request was successful!');

                    this.title = "";
                    this.story = "";
                    this.date = "";

                    Vue.$vToastify.success({
                        title: "Success",
                        body: "Your story was added to this building",
                        position: 'top-right'
                    });

                } else {
                    console.log('Did not work');

                    if (response.data.msg === "guest") {
                        Vue.$vToastify.error({
                            title: "Error",
                            body: "You need to be logged in to perform this action. Please sign up.",
                            position: 'top-right'
                        });
                    }

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

    .building-story {
        text-align: left;
        font-size: 18px;
    }

    .map-side-container {
        background-color: white;
        height: 100%;
        width: 30%;
        text-align: center;
        padding-left: 1em;
        padding-right: 1em;
    }

</style>
