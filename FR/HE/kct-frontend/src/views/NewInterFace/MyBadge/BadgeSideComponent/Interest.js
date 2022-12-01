import React from 'react'
import _ from 'lodash';
import EventTagList from '../BadgePopup/EventTagList'
import './BadgeSlideComponent.css';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render(if allowed) Interest(personal & professional tags) added by the
 * user from  badge editor component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {UserBadge} props.item User details from the badge to show the user describe
 *
 * @class
 * @component
 * @returns {JSX.Element}
 * @constructor
 */
const Intrest = (props) => {
    const {item} = props
    return (
        <div>
            <div>

                {
                    (
                        item.visibility.professional_tags
                        && _.has(item, ['professional_tags'])
                        && !_.isEmpty(item.professional_tags)
                    ) ?
                        <div className="userBadgeProfessionalTags">
                            <EventTagList
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
                    (
                        item.visibility.personal_tags
                        && _.has(item, ['personal_tags'])
                        && !_.isEmpty(item.personal_tags)
                    ) ?
                        <div className="userBadgePersonalTags">
                            <EventTagList
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

export default Intrest 