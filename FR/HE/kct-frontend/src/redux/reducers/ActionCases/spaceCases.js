/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This file provides the reducer methods for the space related reducer actions
 * ---------------------------------------------------------------------------------------------------------------------*/


import {KeepContact as KCT} from '../../types';
import {prepareGridDataData} from '../../utils/pagination';
import {
    findAndUpdateSpaceUserCount,
    reArrangeConversations,
    sliderRearrangementFunc,
    spacesPaginationFetch
} from '../../utils/common.js';

const spacesCases = (state, action) => {

    switch (action.type) {
        case KCT.NEW_INTERFACE.SET_SPACES_DATA:
            const currentSpace = action.payload.current_joined_space.space_uuid;
            state = {
                ...state,
                interfaceSpacesData: action.payload,
                interfaceSliderData: sliderRearrangementFunc(action.payload.spaces, currentSpace)
            }
            break;
        case KCT.NEW_INTERFACE.UPDATE_SPACE_USERS_COUNTS: {
            const spaces = state.interfaceSpacesData.spaces.map((space) => {
                return findAndUpdateSpaceUserCount(space, action.payload);
            })
            const currentSpaceIds = state.interfaceSpacesData.current_joined_space.space_uuid;
            state = {
                ...state,
                interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    spaces: spaces,

                },
                interfaceSliderData: sliderRearrangementFunc(spaces, currentSpaceIds)

            }
        }
            break;
        case KCT.NEW_INTERFACE.SORT_SPACES: {
            const spaces = state.interfaceSpacesData.spaces;
            const current = state.interfaceSpacesData.current_joined_space.space_uuid;
            const pageNo = action.payload;
            state = {
                ...state,
                interfaceSliderData: spacesPaginationFetch(spaces, current, pageNo)
            }
        }
            break;
        case KCT.NEW_INTERFACE.CHANGE_SPACES: {
            const conversations = action.payload.conversations;
            const currentId = action.payload.space.space_uuid;
            const spaces = state.interfaceSpacesData.spaces;
            state = {
                ...state,
                interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    current_space_conversations: reArrangeConversations(conversations),
                    current_joined_space: {
                        space_uuid: currentId
                    }
                },
                interfaceSliderData: sliderRearrangementFunc(spaces, currentId),
                gridPagination: prepareGridDataData(conversations, state)
            }
        }
            break;
        case KCT.NEW_INTERFACE.UPDATE_SPACES_DATA:
            let oldSliderSpaces = state.interfaceSliderData;
            let newSliderData = sliderRearrangementFunc(action.payload.spaces, state.interfaceSpacesData.current_joined_space.space_uuid);
            newSliderData.spaces = newSliderData.spaces.map(space => {
                let oldSpace = oldSliderSpaces.spaces.find(oldSpace => space.space_uuid === oldSpace.space_uuid);
                if (oldSpace) {
                    return {
                        ...oldSpace,
                        ...space,
                        users_count: oldSpace.users_count
                    }
                }
                return space;
            })
            state = {
                ...state,
                interfaceSliderData: newSliderData,
                interfaceSpacesData: {
                    ...state.interfaceSpacesData,
                    spaces: action.payload.spaces,
                },
            }
            break;
    }

    return state;

}

export default spacesCases;