<template>
    <div class="map-side-container">
        <h1 class="title is-3 pt1">Engage With Kilkenny</h1>

        <div v-if="loggedIn" class="flex-1">

            <p v-if="buildingNotSelected">
                Select a building and then you can add a story or some data
            </p>

            <div v-else>

                <div>
                    <p>Do you want to add a story or some data?</p>

                    <button
                        class="button is-medium is-primary"
                        @click="changeFormType('story')"
                    >
                        Add Story
                    </button>

                    <button
                        class="button is-medium is-info"
                        @click="changeFormType('data')"
                    >
                        Add Data
                    </button>
                </div>

                <p class="mb1">
                    Do you have any nice stories about this building?
                </p>

                <input
                    class="input mb1"
                    v-model="title"
                    placeholder="Give your story a nice title"
                    @input="deleteError('title')"
                />

                <textarea
                    v-model="story"
                    rows="4"
                    class="textarea mb1"
                    placeholder="Once upon a time..."
                    @input="deleteError('story')"
                />

                <input
                    class="input mb1"
                    v-model="date"
                    placeholder="When did it happen"
                    @input="deleteError('date')"
                />

                <p v-if="showError" class="is-red mb1">
                    {{ this.error }}
                </p>

                <!-- Display Validation Errors -->
                <div v-if="Object.keys(this.errors).length > 0">
                    <ul class="mb1">
                        <li v-for="error in this.errors" class="is-red">
                            {{ error[0] }}
                        </li>
                    </ul>
                </div>

                <button
                    class="button is-medium is-primary"
                    :class="loading ? 'is-loading' : ''"
                    @click="addStory"
                    :disabled="loading"
                >
                    Add Story
                </button>
            </div>
        </div>

        <div v-else class="flex-1">
            <p>Click any building to see the data associated with it</p>

            <p>Sign up to add stories about buildings</p>

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

        <div class="mb1">
            <p class="mb1">Engage Kilkenny is brought to you by the Heritage Council's Collaborative Town Centre Health Check</p>

            <img src="/assets/HC_logo_retina.png" class="small_heritage_logo" />

            <p>This data has been sourced from <a href="https://openstreetmap.org" target="_blank">OpenStreetMap</a></p>
        </div>

    </div>
</template>

<script>
export default {
    name: "SideMapContainer",
    data () {
        return {
            title: "",
            story: "",
            date: "",
            loading: false,
            showError: false,
            error: "",
            errors: {}
        };
    },
    computed: {
        /**
         * Return True if the building object is null
         */
        buildingNotSelected () {

            if (!window.hasOwnProperty('buildingsMap')) {
                return true;
            }

            return (window.buildingsMap?.building === null);
        },

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
                console.log('add_story', response);

                if (response.data.success) {
                    console.log('The request was successful!');

                    this.title = "";
                    this.story = "";
                    this.date = "";

                    this.$vToastify.success({
                        title: "Success",
                        body: "Your story was added to this building but it needs to be reviewed before its made public.",
                        position: 'top-right'
                    });

                } else {
                    console.log('Did not work');

                    if (response.data.msg === "guest") {
                        this.$vToastify.error({
                            title: "Error",
                            body: "You need to be logged in to perform this action. Please sign up.",
                            position: 'top-right'
                        });
                    }
                }
            })
            .catch(error => {
                console.log('found error');
                console.log(error.response.data);

                this.errors = error.response.data.errors;
            });

            this.loading = false;
        },

        /**
         * Delete one of the errors, if it exists
         */
        deleteError (key) {
            if (this.errors[key]) {
                delete this.errors[key];
            }
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
        display: flex;
        flex-direction: column;
    }

    .small_heritage_logo {
        height: 45px;
        margin-bottom: 0.5em;
    }

</style>
