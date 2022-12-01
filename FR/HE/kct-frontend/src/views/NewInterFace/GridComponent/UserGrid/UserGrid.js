import React, {useEffect, useState} from "react";
import "../GridComponent.css";
import UserGridTitle from "./UserGridTitle";
import UserTile from "../UserTile/UserTile";
import PaginationComp from "../PaginationComp/PaginationComp";
import {connect} from "react-redux";
import newInterfaceActions from "../../../../redux/actions/newInterfaceAction";
import GridHelper from "../GridHelper";

let UserGrid = (props) => {
    const [gridData, setGridData] = useState([]);
    const [gridPagination, setGridPagination] = useState({
        currentPage: 0,
        totalPage: 0,
        currentPageData: [],
    });
    const [currentFocusUser, setCurrentFocusUser] = useState(null);

    let spaceUuid = props.spaces_data?.current_joined_space?.space_uuid;
    const rowPerPage = props.event_data?.event_grid_rows;
    let columns = 12;

    useEffect(() => {
        let rowsData = GridHelper.prepareRowsByConversations(
            props.spaces_data.current_space_conversations,
            columns,
        );
        setGridData(rowsData);
    }, [props.spaces_data.current_space_conversations, rowPerPage]);

    useEffect(() => {
        let totalPages = Math.ceil(gridData.length / (rowPerPage || 4));
        let currentPage = gridPagination.currentPage || 0;

        // decreasing the current page if user is currently beyond the total pages after update
        while (currentPage >= totalPages && currentPage > 0) {
            currentPage--;
        }

        setGridPagination({
            ...gridPagination,
            currentPage,
            totalPage: totalPages,
            currentPageData: updateCurrentPageData(gridPagination.currentPage || 0),
        });

    }, [gridData, props.event_data.event_grid_rows]);

    useEffect(() => {
        let currentPageData = updateCurrentPageData(gridPagination.currentPage);
        setGridPagination({
            ...gridPagination,
            currentPageData,
        });
    }, [gridPagination.currentPage]);

    const mapRowsForUserTile = gridRow => {
        let realUsers = 0;
        let counter = 0;
        gridRow = gridRow.map(conversation => {
            counter++;
            const lastInList = (counter + 1) % columns === 0
                || (counter + 2) % columns === 0
                || counter % columns === 0;
            const lastPersons = (counter + 1) % columns === 0 || counter % columns === 0;
            return {
                ...conversation,
                conversation_users: conversation.conversation_users.map((conversationUser, userIndexInConversation) => {
                    realUsers++;
                    return {
                        ...conversationUser,
                        meta: {
                            userIndexInConversation,
                            lastPersons,
                            lastInList,
                            counter,
                            gridConversation: conversation,
                        }
                    }
                }),
            };
        })
        if (realUsers < columns) {
            for (let i = 0; i < columns - realUsers; i++) {
                gridRow.push({
                    conversation_uuid: 'dummy',
                })
            }
        }
        return gridRow;
    }

    const updateCurrentPageData = (currentPage) => {
        let start = (currentPage * rowPerPage);
        let end = start + rowPerPage;

        let currentPageData = gridData.slice(start, end);
        return currentPageData.map(mapRowsForUserTile)
    }

    return (
        <>
            <UserGridTitle
                currentUser={currentFocusUser}
            />
            <div className="row sm-peopleRow" style={{padding: "20px"}}>
                {
                    gridPagination.currentPageData.map(gridRow => {
                        return gridRow.map((conversation) => conversation.conversation_uuid === 'dummy' ?
                            <div
                                className="col-1 col-sm-1 col-md-1 user-profile"
                                style={{padding: "5px", position: "relative"}}
                            >
                                <div className="blank-div" />
                            </div>
                            :
                            conversation.conversation_users.map(userTile => {
                                    return <UserTile
                                        spaceHost={props.spaceHost}
                                        lastPerson={userTile.meta.lastPersons}
                                        executeScroll={props.executeScroll}
                                        lastInList={userTile.meta.lastInList}
                                        space_uuid={spaceUuid}
                                        position={userTile.meta.userIndexInConversation}
                                        id={userTile.user_id}
                                        is_dummy={userTile.is_dummy}
                                        currentUser={userTile}
                                        counter={userTile.meta.counter}
                                        data={userTile.meta.gridConversation}
                                        setFocusUser={setCurrentFocusUser}
                                    />
                                }
                            )
                        )
                    })
                }
            </div>

            {gridPagination.totalPage > 1 && (
                <PaginationComp
                    current_page={gridPagination.currentPage+1}
                    total_page={gridPagination.totalPage}
                    onPageChange={(page) => {
                        setGridPagination({
                            ...gridPagination,
                            currentPage: page-1,
                        })
                    }}
                />
            )}
        </>
    );
};

const mapStateToProps = (state) => {
    return {
        gridPagination: state.NewInterface.gridPagination,

        event_data: state.NewInterface.interfaceEventData,
        spaces_data: state.NewInterface.interfaceSpacesData,
        gridMaxRow: state.Dashboard.gridState.maxRow,
        gridMaxColumn: state.Dashboard.gridState.maxColumn,
        spaceHost: state.NewInterface.interfaceSpaceHostData,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        triggerPagination: (data) => dispatch(newInterfaceActions.NewInterFace.triggerPagination(data)),
    }
}

UserGrid = connect(mapStateToProps, mapDispatchToProps)(UserGrid);
export default UserGrid;