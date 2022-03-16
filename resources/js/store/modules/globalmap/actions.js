export const actions = {
    /**
     * Get the art point data for the global map
     */
    async GET_ART_DATA (context)
    {
        await axios.get('/global/art-data')
            .then(response => {
                console.log('get_art_data', response);

                context.commit('globalArtData', response.data);
            })
            .catch(error => {
                console.error('get_art_data', error);
            });
    },

    /**
     * Return the geojson files
     */
    async GET_ALL_LAYERS (context)
    {
        await axios.get('/global/buildings')
            .then(response => {
                console.log('get_buildings', response);

                if (response.data.success)
                {
                    context.commit('setBuildings', response.data.buildings);
                    context.commit('setStreets', JSON.parse(response.data.streets));
                    context.commit('setWalls', JSON.parse(response.data.walls));
                    context.commit('setPoints', JSON.parse(response.data.points));
                }
            })
            .catch(error => {
                console.error('get_buildings', error);
            });
    },

    /**
     * Get all stories for all buildings
     */
    async GET_ALL_STORIES (context)
    {
        await axios.get('/buildings/all-stories')
            .then(response => {
                console.log('all_stories', response);

                context.commit('addStoriesToBuildings', response.data);
            })
            .catch(error => {
                console.log('all_stories', error);
            });
    },

    /**
     * Get clusters for the global map
     */
    async GET_CLUSTERS (context, payload)
    {
        await axios.get('/global/clusters', {
            params: {
                zoom: payload,
                bbox: null
            }
        })
        .then(response => {
            console.log('get_clusters', response);

            context.commit('updateGlobalData', response.data);
        })
        .catch(error => {
            console.error('get_clusters', error);
        });
    }
}
