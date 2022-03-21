export const actions = {

    async ADMIN_GET_STORIES (context)
    {
        await axios.get('/admin/stories/get-stories')
            .then(response => {
                console.log('admin_get_stories', response);

                context.commit('adminGetStories', response.data);
            })
            .catch(error => {
                console.log('admin_get_stories', error);
            });
    },

    async ADMIN_GET_DATA (context)
    {
        await axios.get('/admin/review/get-data')
            .then(response => {
                console.log('admin_get_data', response);

                context.commit('adminGetData', response.data);
            })
            .catch(error => {
                console.log('admin_get_data', error);
            });
    }

};
