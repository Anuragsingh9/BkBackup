import SpaceData from "./SpaceData";

/**
 * @type {Object}
 * @property {SpaceData[]} spaces All the spaces of current event sorted by type of them
 * @property {Number} maxPage Count of the pages calculated with respect to number of space
 */
const InterfaceSliderData = {
    spaces: [
        SpaceData
    ],
    maxPage: 0,
};

export default InterfaceSliderData;
