/**
 * @type {Object}
 * @property {Number} asset_id Id of the asset
 * @property {ColorRGBA} asset_default_color Default color of the asset in RGBA
 * @property {String} asset_path URL of the asset
 * @property {String} asset_thumbnail_path URL of asset thumbnail
 */
const Asset = {
    asset_default_color: {
        r: 0,
        g: 0,
        b: 0,
        a: 1
    },
    asset_id: 1,
    asset_path: "https://s3.eu-west-2.amazonaws.com/kct-dev/testingaccount.humannconnect.dev/users/avatar/0c5dd3c1-2148-4fad-95e1-fbbed20f5067.",
    asset_thumbnail_path: "https://s3.eu-west-2.amazonaws.com/kct-dev/testingaccount.humannconnect.dev/users/avatar/0c5dd3c1-2148-4fad-95e1-fbbed20f5067."
}

export default Asset;