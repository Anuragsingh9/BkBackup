import React from 'react'
import './ShowPersonlInfo.css';
import _ from 'lodash';
import './BadgeSlideComponent.css';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render(if allowed) asked question by user  from 'QUESTIONS I HAVE'
 * section in badge editor component.
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
const Questions = (props) => {
    const {item} = props;
    const fieldName_3 = item.personal_info.field_3 !== ''
    && !_.isEmpty(item.personal_info.field_3)
        ? `${item.personal_info.field_3}`
        : '';
    return (
        (_.has(item, ['visibility'])) ?
            (
                <div>
                    {
                        item.visibility.p_field_3 == 0
                            ? ""
                            : (
                                item.personal_info.field_3 !== ''
                                && item.personal_info.field_3
                                && !_.isEmpty(item.personal_info.field_3)
                                && <p
                                    className="p-0 questionIcon col-md-12 col-lg-12 badge-bottom-content"
                                >
                                    <span></span>
                                    {fieldName_3}
                                </p>
                            )
                    }
                </div>
            )
            : (
                <div>
                    {
                        (
                            item.personal_info.field_3
                            && item.personal_info.field_3 !== ''
                            && !_.isEmpty(item.personal_info.field_3)
                            && <p
                                className="p-0 col-md-12 col-lg-12 badge-bottom-content questionIcon"
                            >
                                {item.personal_info.field_3}
                            </p>
                        )
                    }
                </div>
            )
    )
}


export default Questions 