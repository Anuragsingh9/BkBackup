import React, {Component} from 'react'
import Helper from '../../../../Helper';
import './ShowPersonlInfo.css';
import Svg from "../../../../Svg";
import _ from 'lodash';

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to render user's company & union details in user badge component if
 * it is allowed to show fro badge editor component.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent component
 * @param {UserBadge} props.item User details from the badge to show the user describe
 *
 * @class
 * @component
 * @constructor
 */
class ShowPersonlInfo extends Component {
    render() {
        const {item} = this.props;
        const fieldName_1 = item.personal_info.field_1 !== ''
        && !_.isEmpty(item.personal_info.field_1)
            ? `${item.personal_info.field_1}`
            : '';
        const fieldName_2 = item.personal_info.field_2 !== ''
        && !_.isEmpty(item.personal_info.field_2)
            ? `${item.personal_info.field_2}`
            : '';
        const fieldName_3 = item.personal_info.field_3 !== ''
        && !_.isEmpty(item.personal_info.field_3)
            ? `${item.personal_info.field_3}`
            : '';

        if (_.has(item, ['visibility'])) {
            return (
                <div>
                    {
                        item.visibility.p_field_1 == 0
                            ? ""
                            : (item.personal_info.field_1 !== ''
                                && item.personal_info.field_1
                                && !_.isEmpty(item.personal_info.field_1)
                                && <p
                                    className="p-0 heartIcon col-md-12 col-lg-12 badge-bottom-content"
                                    title={fieldName_1.length > 30 ? fieldName_1 : ''}
                                >
                                    <span
                                        className="svgicon ICOblue"
                                        dangerouslySetInnerHTML={{__html: Svg.ICON.likeHeart}}
                                    ></span>
                                    {Helper.limitText(fieldName_1, 30)}
                                </p>
                            )
                    }
                    {
                        item.visibility.p_field_2 == 0
                            ? ""
                            : (item.personal_info.field_2 !== ''
                                && item.personal_info.field_2
                                && !_.isEmpty(item.personal_info.field_2)
                                && <p
                                    className="p-0 searchIcon col-md-12 col-lg-12 badge-bottom-content"
                                    title={fieldName_2.length > 30 ? fieldName_2 : ''}
                                >
                                    <span
                                        className="svgicon ICOsrch"
                                        dangerouslySetInnerHTML={{__html: Svg.ICON.magnifyingGlass}}
                                    ></span>
                                    {Helper.limitText(fieldName_2, 30)}
                                </p>
                            )
                    }
                    {
                        item.visibility.p_field_3 == 0
                            ? ""
                            : (item.personal_info.field_3 !== ''
                                && item.personal_info.field_3
                                && !_.isEmpty(item.personal_info.field_3)
                                && <p
                                    className="p-0 questionIcon col-md-12 col-lg-12 badge-bottom-content"
                                    title={fieldName_3.length > 30 ? fieldName_3 : ''}
                                >
                                    <span
                                        className="svgicon ICOqsnmrk"
                                        dangerouslySetInnerHTML={{__html: Svg.ICON.questionMark}}
                                    ></span>
                                    {Helper.limitText(fieldName_3, 30)}</p>
                            )
                    }
                </div>
            )
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
        }
    }
}

export default ShowPersonlInfo;
