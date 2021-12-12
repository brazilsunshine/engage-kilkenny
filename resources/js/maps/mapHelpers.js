import i18n from '../i18n';
import moment from 'moment';

const helper = {
    /**
     * These options control how the popup renders
     * @see https://leafletjs.com/reference-1.7.1.html#popup-l-popup
     */
    popupOptions: {
        minWidth: 350,
        maxWidth: 600,
        maxHeight: 700,
        closeButton: true,
        autoPanPaddingTopLeft: 20,
        autoPanPaddingBottomRight: 20
    },

    /**
     * Returns th HTML that displays tags on Photo popups
     *
     * @param tagsString
     * @returns {string}
     */
    parseTags: (tagsString) => {
        if (!tagsString) {
            return i18n.t('litter.not-verified');
        }

        let tags = '';
        let a = tagsString.split(',');

        a.pop();

        a.forEach(i => {
            let b = i.split(' ');

            tags += i18n.t('litter.' + b[0]) + ': ' + b[1] + '<br>';
        });

        return tags;
    },

    /**
     * Formats the user name for usage in Photo popups
     *
     * @param name
     * @param username
     * @returns {string}
     */
    formatUserName: (name, username) => {
        return (name || username)
            ? `${i18n.t('locations.cityVueMap.by')} ${name ? name : ''} ${username ? '@' + username : ''}`
            : '';
    },

    /**
     * Formats the team name for usage in Photo popups
     *
     * Todo translate 'team'
     * @param teamName
     * @returns {string}
     */
    formatTeam: (teamName) => {
        return teamName
            ? `Team ${teamName}`
            : '';
    },

    /**
     * Formats the photo taken time for usage in Photo popups
     *
     * @param takenOn
     * @returns {string}
     */
    formatPhotoTakenTime: (takenOn) => {
        return i18n.t('locations.cityVueMap.taken-on') + ' ' + moment(takenOn).format('LLL');
    },

    /**
     * Returns the HTML that displays the Photo popups
     *
     * @param imageUrl
     * @param tagsString
     * @param takenOn
     * @param user
     * @param team
     * @returns {string}
     */
    getMapImagePopupContent: (imageUrl, tagsString, takenOn, user, team) => {
        const tags = helper.parseTags(tagsString);
        const takenDateString = helper.formatPhotoTakenTime(takenOn);
        const teamFormatted = helper.formatTeam(team);

        return `
            <img
                src="${imageUrl}"
                class="leaflet-litter-img"
                onclick="document.querySelector('.leaflet-popup-close-button').click();"
                alt="Litter photo"
            />
            <div class="leaflet-litter-img-container">
                <p>${tags}</p>
                <p>${takenDateString}</p>
                ${user || teamFormatted ? ('<p>' + user + '<br>' + teamFormatted + '</p>') : ''}
            </div>`;
    }
};

export {helper as mapHelper};
