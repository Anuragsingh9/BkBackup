import ColorRGBA from "./ColorRGBA";

/**
 * @type {Object}
 * @property {Number} asset_id Scenery ID
 * @property {ColorRGBA} top_background_color Color of scenery if its color type
 * @property {Number} component_opacity Opacity of scenery image
 * @property {ColorRGBA} asset_color Asset color to apply on scenery
 * @property {String} asset_path Path of scenery image
 * @property {Number} category_type Category of scenery if its image or color
 */
const SceneryData = {
    asset_id: 1,
    top_background_color: ColorRGBA,
    component_opacity: 0.3,
    asset_color: ColorRGBA,
    asset_path: 'asset_path',
    category_type: 1,
};

export default SceneryData;
