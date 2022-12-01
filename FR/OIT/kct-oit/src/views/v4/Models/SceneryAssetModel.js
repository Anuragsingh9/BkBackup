import ColorRGBA from "../../../Models/ColorRGBA";

/**
 * @type {Object}
 * @property {ColorRGBA} asset_default_color Color value of asset
 * @property {Number} asset_id Id of asset
 * @property {String} asset_path Image path of asset
 * @property {String} asset_thumbnail_path Image thumbnail path
 */
const SceneryAssetModel = {
    asset_default_color: ColorRGBA,
    asset_id: 1,
    asset_path: 'asset_path',
    asset_thumbnail_path: 'asset_thumbnail_path',
}

export default SceneryAssetModel;