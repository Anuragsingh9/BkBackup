/**
 * @type {Object}
 * @property {Boolean} componentVisibility Visibility of the component player set by pilot
 * @property {Number} currentMediaType Current selected type of content, e.g. 1 for Video
 * @property {Object} currentMediaData Current data for the media
 * @property {String} currentMediaData.value Url value for the media
 * @property {Boolean} isZoomMute To indicate if zoom is muted
 * @property {String[]} zoomJoinedUsers Joined users on zoom
 * @property {Boolean} isVideoPlayerLoaded To indicate if video player is loaded or not
 * @property {Boolean} showMuteButtonText To indicate if mute button text need to show or not
 */
const ContentManagementMeta =  {
    componentVisibility: 0,
    currentMediaType: 1,
    currentMediaData: {},
    isZoomMute: false,
    zoomJoinedUsers: [],
    isVideoPlayerLoaded: true,
    showMuteButtonText: false,
};

export default ContentManagementMeta;
