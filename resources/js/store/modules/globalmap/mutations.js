import { init } from './init'

export const mutations = {

    /**
     * Loop over all buildings
     */
    addStoriesToBuildings (state, payload)
    {
        console.log("5 stories", payload); // 3 Ids
        // console.log("All buildings", state.buildingsGeojson.features); // ~4000 buildings

        state.buildingsGeojson.features.forEach(building => {

            if (payload.includes(parseInt(building.properties.osm_id)))
            {
                building.properties.hasStory = true;
            }
            else
            {
                building.properties.hasStory = false;
            }
        });
    },

    /**
     *
     */
	closeDatesButton (state)
    {
		state.datesOpen = false;
	},

    /**
     *
     */
	closeLangsButton (state)
    {
		state.langsOpen = false;
	},

    /**
     * Initialise the global art data
     */
    globalArtData (state, payload)
    {
        state.artData = payload;
    },

    /**
     * When changing dates
     */
	globalLoading (state, payload)
    {
		state.loading = payload;
	},

    /**
     * Reset the state, when the user logs out.
     */
    resetState (state)
    {
        Object.assign(state, init);
    },

    /**
     * Buildings.geojson is fetched from the backend
     */
    setBuildings (state, payload)
    {
        state.buildingsGeojson = payload;
    },

    /**
     *
     */
	toggleLangsButton (state)
    {
		state.langsOpen = ! state.langsOpen;
	},

    /**
     *
     */
	toggleGlobalDates (state)
    {
		state.datesOpen = ! state.datesOpen;
	},

	// updateCurrentGlobalDate (state, payload)
    // {
	// 	state.currentDate = payload;
	// },

    /**
     *
     */
	updateGlobalData (state, payload)
    {
		state.geojson = payload;
	}

};
