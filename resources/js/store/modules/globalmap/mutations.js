import { init } from './init'

export const mutations = {

    /**
     * Loop over all buildings
     */
    addStoriesToBuildings (state, payload)
    {
        state.buildings.features.forEach(building => {

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
        // Delete missing data. Should be preprocessed before loading the file but we will just do it here
        payload.features = payload.features.map(feature => {
            Object.keys(feature.properties).map(buildingsKey => {
                if (!feature.properties[buildingsKey]) delete feature.properties[buildingsKey];
            });

            return feature;
        });

        state.buildings = payload;
    },

    /**
     * Streets.geojson is fetched from the backend
     */
    setStreets (state, payload)
    {
        // Delete missing data. Should be preprocessed before loading the file but we will just do it here
        payload.features = payload.features.map(feature => {
            Object.keys(feature.properties).map(streetKey => {
               if (!feature.properties[streetKey]) delete feature.properties[streetKey];
            });

            return feature;
        });

        state.streets = payload;
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
