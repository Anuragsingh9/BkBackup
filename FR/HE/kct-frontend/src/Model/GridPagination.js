/**
 * @type {Object}
 * @property {Number} current_page Current page number on grid
 * @property {Number} totalpages Total number of pages possible with conversations count
 * @property {ConversationData[][]} currentPageData Conversations data divided into rows for current page
 */
const GridPagination = {
    current_page: 1,
    totalpages: 1,
    currentPageData: []
};

export default GridPagination;
