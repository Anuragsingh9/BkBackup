import React, {Component} from 'react'
import _ from 'lodash';
import EventTagList from '../BadgePopup/EventTagList'
import ShowPersonlInfo from './ShowPersonlInfo';
import "./ShowUserBadge.css";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a wrapper component which is developed to render user's personal details(name and profile
 * picture) + personal & professional tags(If added any) on the user badge component to show other users.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {UserBadge} props.item User details from the badge to show the user describe
 *
 * @class
 * @component
 * @constructor
 */
class ShowUserBadge extends Component {
    render() {
        const {item} = this.props
        return (
            <div>
                <ShowPersonlInfo item={item} />
                <div>
                    {
                        (item.visibility.professional_tags
                            && _.has(item, ['professional_tags'])
                            && !_.isEmpty(item.professional_tags)
                        ) ?
                            <div className="UserbadgeProTags">
                                {/* <h5 class="proTagHeading">Professional tag</h5> */}
                                <EventTagList
                                    type={'1'}
                                    paginate={true}
                                    isEditable={false}
                                    isLoading={false}
                                    data={(item.professional_tags) ? item.professional_tags : []}
                                />
                            </div>
                            :
                            null
                    }
                </div>
                <div>
                    {
                        (item.visibility.personal_tags
                            && _.has(item, ['personal_tags'])
                            && !_.isEmpty(item.personal_tags)) ?
                            <div className="UserbadgePerTags">
                                {/* <h5 class="perTagHeading">Personal tag</h5> */}
                                <EventTagList
                                    type={'2'}
                                    paginate={true}
                                    isEditable={false}
                                    isLoading={false}
                                    data={(item.personal_tags) ? item.personal_tags : []}
                                />
                            </div>
                            :
                            null
                    }
                </div>

            </div>
        )
    }
}

export default ShowUserBadge;
