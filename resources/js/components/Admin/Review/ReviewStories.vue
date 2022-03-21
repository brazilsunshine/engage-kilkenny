<template>
    <div v-if="stories.length > 0">
        <div class="verify-stories-container">
            <p style="font-size: 1.5rem;">Verify story: </p>

            <p class="flex-1" style="font-size: 1.5rem; margin-left: 1em;">#{{ stories[0].id }}</p>

            <p>You have {{ stories.length }} stories to verify</p>
        </div>

        <div>
            <p>Title:</p>

            <input
                class="input mb1"
                v-model="stories[0].title"
            />

            <p>Story:</p>

            <textarea
                class="input mb1"
                v-model="stories[0].story"
                rows="4"
                style="height: auto;"
            />

            <p>Date:</p>

            <input
                class="input mb3"
                v-model="stories[0].date"
            />

            <div class="has-text-centered">
                <button class="button is-danger mr2" @click="deleteStory">Delete</button>

                <button class="button is-primary" @click="acceptStory">Accept</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ReviewStories",
    props: [
        'stories'
    ],
    methods: {
        /**
         * Mark a story as reviewed
         */
        async acceptStory () {

            await axios.post('/admin/stories/accept-story', {
                story: this.stories[0]
            })
            .then(response => {
                console.log('admin.accept_story', response);

                if (response.data.success)
                {
                    const title = "Success !";
                    const body = "Story #" + this.stories[0].id + " has been accepted.";

                    this.stories.shift();

                    this.$vToastify.success({
                        title,
                        body,
                        position: 'bottom-right'
                    });
                }
            })
            .catch(error => {
                console.log('admin.error.accept_story', error);
            });

        },

        /**
         * Delete a story from our database
         */
        async deleteStory () {

            await axios.post('/admin/stories/delete-story', {
                story_id: this.stories[0].id
            })
            .then(response => {
                console.log('admin.delete-story', response);

                if (response.data.success)
                {
                    const title = "Success !";
                    const body = "Story #" + this.stories[0].id + " has been deleted.";

                    this.stories.shift();

                    this.$vToastify.success({
                        title,
                        body,
                        position: 'bottom-right'
                    });
                }
            })
            .catch(error => {
                console.log('admin.error.delete-story', error);
            });
        }
    }
}
</script>

<style scoped>

</style>
