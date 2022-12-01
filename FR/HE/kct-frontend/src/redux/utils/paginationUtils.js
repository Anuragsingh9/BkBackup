/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description this file contains the dispatcher for pagination row and column related methods with sorting applied
 * ---------------------------------------------------------------------------------------------------------------------
 */


import _ from 'lodash';


export const rowsGenerator = (conversations, currentPage, state) => {

    const spaceHost = state.interfaceSpaceHostData;

    let data = [];

    let counter = 0;

    let position = 0;


    const maxRows = Number(localStorage.getItem('event_grid_rows'));

    let row = 1;

    conversations.map((val, key) => {

        const usersData = !_.isEmpty(spaceHost) ? filterSpaceHost(spaceHost[0].user_id, val.conversation_users) : val.conversation_users;

        if (_.isEmpty(data)) {
            data.push([]);
        }


        const spacesLeft = (12 - (counter % 12));
        if (spacesLeft >= usersData.length) {

            counter = counter + usersData.length;

            data[row - 1].push(val);
            if (counter != 0 && counter % 12 == 0) {
                row = row + 1;
                data.push([]);
            }

        } else {
            counter = counter + (usersData.length) + spacesLeft;

            data[row - 1].push({conversation_type: 'dummy', conversation_users: Array(spacesLeft).fill(1)});
            row = row + 1;
            data.push([]);
            data[row - 1].push(val);

        }

    })
    const maxPage = Math.ceil(row / maxRows);
    let currentPageData = [];

    if (currentPage < maxPage) {
        const startPoint = (maxRows * (currentPage - 1));
        const endPoint = (maxRows * (currentPage - 1)) + maxRows;
        currentPageData = data.slice(startPoint, endPoint);

    } else if (currentPage == maxPage) {
        const startPoint = (maxRows * (currentPage - 1));
        const endPoint = (data.length);
        currentPageData = data.slice(startPoint, endPoint);
    }

    return {
        current_page: currentPage,
        totalpages: maxPage,
        currentPageData: currentPageData
    };
}

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This function will filter spacehost(system role) from received data.
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {String} id Space host unique id
 * @param {Array} users Array of users data
 * @returns {Object}
 */
const filterSpaceHost = (id, users) => {
    const flag = users.filter((val) => {
        if (val.user_id != id) {
            return val
        }
    });
    return flag;
}