import React from 'react'
import _ from 'lodash';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render description text added by the user from 'WHO I AM' section in
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
const Describe = (props) => {
    const {item} = props;
    const fieldName_1 = item.personal_info.field_1 !== ''
    && !_.isEmpty(item.personal_info.field_1)
        ? `${item.personal_info.field_1}`
        : '';

    // conditional rendering acording to  visibility 
    if (_.has(item, ['visibility'])) {
        return (
            <div>
                {
                    item.visibility.p_field_1 == 0
                        ? ""
                        : (
                            item.personal_info.field_1 !== ''
                            && item.personal_info.field_1
                            && !_.isEmpty(item.personal_info.field_1)
                            && <p
                                className="p-0 heartIcon col-md-12 col-lg-12 badge-bottom-content">
                                <span></span>
                                {fieldName_1}
                            </p>
                        )
                }
            </div>
        )
        // when visibility off    
    } else {
        return (
            <div>
                {
                    (
                        item.personal_info.field_1
                        && item.personal_info.field_1 !== ''
                        && !_.isEmpty(item.personal_info.field_1)
                        && <p
                            className="p-0 col-md-12 col-lg-12 badge-bottom-content heartIcon"
                        >
                            {item.personal_info.field_1}
                        </p>
                    )
                }
            </div>
        )
    }

}
export default Describe;