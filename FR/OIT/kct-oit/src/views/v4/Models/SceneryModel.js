import SceneryAssetModel from "./SceneryAssetModel";

/**
 * @type {Object}
 * @property {Number} category_id Id of the category
 * @property {String} category_type type of the category 1, for picture with color, 2 for asset color
 * @property {String} category_name Name of the category
 * @property {SceneryAssetModel[]} category_assets Assets of Category
 * @property {Object[]} category_locales Category locale values
 * @property {String} category_locales.[].value Value of locale
 * @property {String} category_locales.[].locale Name of the locale
 */
const SceneryModel = {
    category_id: 1,
    category_type: 2,
    category_name: "Monochrome",
    category_assets: [SceneryAssetModel],
    category_locales: [{value: "Monochrome", locale: "en"}, {value: "Monochrome", locale: "fr"}],
}

export default SceneryModel;