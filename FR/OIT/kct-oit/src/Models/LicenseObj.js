
/**
 * @type {Object}
 * @property {Number} key Key for license
 * @property {Object} data Object which holds enable/disable state of a license
 * @property {Object} data.enabled License's enable/disable state
 * @property {Number} group_id Method name eg - GET/POST
 * @property {String} _method Method name eg - GET/POST
 */

const LicenseObj = {
    key: "custom_zoom_settings",
    data: {
        enabled: 0
    },
    group_id: 1,
    _method: "PUT"
}

export default LicenseObj;