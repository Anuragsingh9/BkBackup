import React from 'react'
import './ShowPersonlInfo.css';
import _ from 'lodash';
import './BadgeSlideComponent.css';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render(if allowed) asked question by user  from 'MY SEARCHES' section in
 * badge editor component.
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
const Searches = (props) => {
    const {item} = props;
    const fieldName_2 = item.personal_info.field_2 !== ''
    && !_.isEmpty(item.personal_info.field_2)
        ? `${item.personal_info.field_2}`
        : '';

    return (
        (_.has(item, ['visibility'])) ?
            (<div>
                {
                    item.visibility.p_field_2 == 0
                        ? ""
                        : (item.personal_info.field_2 !== ''
                            && item.personal_info.field_2
                            && !_.isEmpty(item.personal_info.field_2)
                            && <p
                                className="p-0 searchIcon col-md-12 col-lg-12 badge-bottom-content"
                            >
                                {fieldName_2}
                            </p>
                        )
                }
            </div>)
            : (<div>
                {
                    (
                        item.personal_info.field_2
                        && item.personal_info.field_2 !== ''
                        && !_.isEmpty(item.personal_info.field_2)
                        && <p
                            className="p-0 col-md-12 col-lg-12 badge-bottom-content searchIcon"
                        >
                            {item.personal_info.field_2}
                        </p>
                    )
                }
            </div>)
    )
}

export default Searches 